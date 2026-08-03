const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const {
    cleanupUploadedFile,
    conflictError,
    notFound,
    removeFile,
    serverError,
} = require('../utils/apiResponse');
const { createId } = require('../utils/id');
const { getPagination, sendListResponse } = require('../utils/pagination');

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
        const pagination = getPagination(req.query);
        const replacements = [];
        const where = [];
        const search = String(req.query.search || '').trim();

        if (search) {
            const keyword = `%${search}%`;
            where.push('(name LIKE ? OR description LIKE ?)');
            replacements.push(keyword, keyword);
        }

        const whereClause = where.length ? `WHERE ${where.join(' AND ')}` : '';
        const countResult = pagination
            ? await sequelize.query(`SELECT COUNT(*) AS total FROM clients ${whereClause}`, { replacements, type: QueryTypes.SELECT })
            : [{ total: 0 }];
        const dataReplacements = [...replacements];
        const paginationSql = pagination ? ' LIMIT ? OFFSET ?' : '';
        if (pagination) dataReplacements.push(pagination.limit, pagination.offset);
        const clients = await sequelize.query(
            `SELECT * FROM clients ${whereClause} ORDER BY name ASC${paginationSql}`,
            { replacements: dataReplacements, type: QueryTypes.SELECT }
        );
        sendListResponse(res, clients, pagination, countResult[0]?.total);
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

        const id = createId();
        await sequelize.query(
            'INSERT INTO clients (id, name, client_icon, description, createdAt, updatedAt) VALUES (?, ?, ?, ?, NOW(), NOW())',
            {
                replacements: [id, name, client_icon, description],
                type: QueryTypes.INSERT
            }
        );
        res.status(201).json({ id, name, client_icon, description });
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
