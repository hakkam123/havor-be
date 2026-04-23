const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const fs = require('fs');
const path = require('path');

const getAllClients = async (req, res) => {
    try {
        const clients = await sequelize.query(
            'SELECT * FROM clients ORDER BY name ASC',
            { type: QueryTypes.SELECT }
        );
        res.json(clients);
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
};

const createClient = async (req, res) => {
    const { name, description } = req.body; 
    const client_icon = req.file ? `/uploads/clients/${req.file.filename}` : null;
    try {
        const result = await sequelize.query(
            'INSERT INTO clients (name, client_icon, description, createdAt, updatedAt) VALUES (?, ?, ?, NOW(), NOW())',
            {
                replacements: [name, client_icon, description],
                type: QueryTypes.INSERT
            }
        );
        res.status(201).json({ id: result[0], name, client_icon, description });
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
};

const updateClient = async (req, res) => {
    const { name, description } = req.body;
    const { id } = req.params;
    let client_icon = null;

    try {
        const existingClient = await sequelize.query(
            'SELECT * FROM clients WHERE id = ?',
            { replacements: [id], type: QueryTypes.SELECT }
        );

        if (existingClient.length === 0) {
            return res.status(404).json({ message: 'Client not found' });
        }

        if (req.file) {
            client_icon = `/uploads/clients/${req.file.filename}`;
        } else {
            client_icon = existingClient[0].client_icon;
        }

        await sequelize.query(
            'UPDATE clients SET name = ?, client_icon = ?, description = ?, updatedAt = NOW() WHERE id = ?',
            {
                replacements: [name, client_icon, description, id],
                type: QueryTypes.UPDATE
            }
        );
        res.json({ message: 'Client updated successfully' });
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
};

const deleteClient = async (req, res) => {
    try {
        const clientResult = await sequelize.query(
            'SELECT * FROM clients WHERE id = ?',
            { replacements: [req.params.id], type: QueryTypes.SELECT }
        );

        if (clientResult.length > 0) {
            const client = clientResult[0];
            if (client.client_icon) {
                const iconPath = path.join(__dirname, '..', '..', client.client_icon.replace('/uploads/', 'uploads/'));
                if (fs.existsSync(iconPath)) {
                    fs.unlinkSync(iconPath);
                }
            }
        }

        await sequelize.query(
            'DELETE FROM clients WHERE id = ?',
            { replacements: [req.params.id], type: QueryTypes.DELETE }
        );
        res.json({ message: 'Client removed' });
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
};

module.exports = {
    getAllClients,
    createClient,   
    updateClient,
    deleteClient
};