const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const {
    cleanupUploadedFile,
    conflictError,
    notFound,
    removeFile,
    serverError,
} = require('../utils/apiResponse');

const ensureUniqueName = async (name, ignoreId = null) => {
    const replacements = ignoreId ? [name, ignoreId] : [name];
    const condition = ignoreId ? 'LOWER(name) = LOWER(?) AND id != ?' : 'LOWER(name) = LOWER(?)';
    const existing = await sequelize.query(
        `SELECT id FROM clients WHERE ${condition} LIMIT 1`,
        { replacements, type: QueryTypes.SELECT }
    );
    return existing.length === 0;
};

const getAllClients = async (req, res) => {
    try {
        const clients = await sequelize.query(
            'SELECT * FROM clients ORDER BY name ASC',
            { type: QueryTypes.SELECT }
        );
        res.json(clients);
    } catch (error) {
        serverError(res, error);
    }
};

const createClient = async (req, res) => {
    const { name, description } = req.body; 
    const client_icon = req.file ? `/uploads/clients/${req.file.filename}` : null;
    try {
        if (!(await ensureUniqueName(name))) {
            cleanupUploadedFile(req);
            return conflictError(res, 'name', 'Client name already exists');
        }

        const result = await sequelize.query(
            'INSERT INTO clients (name, client_icon, description, createdAt, updatedAt) VALUES (?, ?, ?, NOW(), NOW())',
            {
                replacements: [name, client_icon, description],
                type: QueryTypes.INSERT
            }
        );
        res.status(201).json({ id: result[0], name, client_icon, description });
    } catch (error) {
        cleanupUploadedFile(req);
        serverError(res, error);
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
            cleanupUploadedFile(req);
            return notFound(res, 'Client not found');
        }

        if (name && !(await ensureUniqueName(name, id))) {
            cleanupUploadedFile(req);
            return conflictError(res, 'name', 'Client name already exists');
        }

        if (req.file) {
            removeFile(existingClient[0].client_icon);
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
        cleanupUploadedFile(req);
        serverError(res, error);
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
            removeFile(client.client_icon);
        } else {
            return notFound(res, 'Client not found');
        }

        await sequelize.query(
            'DELETE FROM clients WHERE id = ?',
            { replacements: [req.params.id], type: QueryTypes.DELETE }
        );
        res.json({ message: 'Client removed' });
    } catch (error) {
        serverError(res, error);
    }
};

module.exports = {
    getAllClients,
    createClient,   
    updateClient,
    deleteClient
};
