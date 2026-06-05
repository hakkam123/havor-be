const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const {
  cleanupUploadedFile,
  conflictError,
  notFound,
  removeFile,
  serverError,
} = require('../utils/apiResponse');
const { getPagination, sendListResponse } = require('../utils/pagination');

const ensureUniqueName = async (name, ignoreId = null) => {
  const replacements = ignoreId ? [name, ignoreId] : [name];
  const condition = ignoreId ? 'LOWER(name) = LOWER(?) AND id != ?' : 'LOWER(name) = LOWER(?)';
  const existing = await sequelize.query(
    `SELECT id FROM expertises WHERE ${condition} LIMIT 1`,
    { replacements, type: QueryTypes.SELECT }
  );
  return existing.length === 0;
};

// @desc    Get all expertises
// @route   GET /api/expertise
// @access  Public
const getAllExpertises = async (req, res) => {
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
      ? await sequelize.query(`SELECT COUNT(*) AS total FROM expertises ${whereClause}`, { replacements, type: QueryTypes.SELECT })
      : [{ total: 0 }];
    const dataReplacements = [...replacements];
    const paginationSql = pagination ? ' LIMIT ? OFFSET ?' : '';
    if (pagination) dataReplacements.push(pagination.limit, pagination.offset);
    const data = await sequelize.query(
      `SELECT * FROM expertises ${whereClause} ORDER BY createdAt DESC${paginationSql}`,
      { replacements: dataReplacements, type: QueryTypes.SELECT }
    );
    sendListResponse(res, data, pagination, countResult[0]?.total);
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Create expertise
// @route   POST /api/expertise
// @access  Private/Admin
const createExpertise = async (req, res) => {
  const { name, description } = req.body;
  const icon_url = req.file ? `/uploads/expertise/${req.file.filename}` : null;
  try {
    if (!(await ensureUniqueName(name))) {
      cleanupUploadedFile(req);
      return conflictError(res, 'name', 'Expertise name already exists');
    }

    const [result] = await sequelize.query(
      `INSERT INTO expertises (name, description, icon_url, createdAt, updatedAt) 
       VALUES (?, ?, ?, NOW(), NOW())`,
      {
        replacements: [name, description, icon_url],
        type: QueryTypes.INSERT
      }
    );
    res.status(201).json({ id: result, name, description, icon_url });
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

// @desc    Update expertise
// @route   PUT /api/expertise/:id
// @access  Private/Admin
const updateExpertise = async (req, res) => {
  try {
    const results = await sequelize.query(
      'SELECT * FROM expertises WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );
    
    if (results.length > 0) {
      const data = results[0];
      const { name, description } = req.body;
      let icon_url = data.icon_url;

      if (name && !(await ensureUniqueName(name, req.params.id))) {
        cleanupUploadedFile(req);
        return conflictError(res, 'name', 'Expertise name already exists');
      }

      if (req.file) {
        removeFile(data.icon_url);
        icon_url = `/uploads/expertise/${req.file.filename}`;
      }

      await sequelize.query(
        `UPDATE expertises SET 
          name = ?, 
          description = ?, 
          icon_url = ?, 
          updatedAt = NOW() 
         WHERE id = ?`,
        {
          replacements: [
            name || data.name,
            description || data.description,
            icon_url,
            req.params.id
          ],
          type: QueryTypes.UPDATE
        }
      );
      res.json({ id: req.params.id, name, description, icon_url });
    } else {
      cleanupUploadedFile(req);
      notFound(res, 'Expertise not found');
    }
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

// @desc    Delete expertise
// @route   DELETE /api/expertise/:id
// @access  Private/Admin
const deleteExpertise = async (req, res) => {
  try {
    const results = await sequelize.query(
      'SELECT * FROM expertises WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );

    if (results.length > 0) {
      const data = results[0];
      removeFile(data.icon_url);
      await sequelize.query(
        'DELETE FROM expertises WHERE id = ?',
        { replacements: [req.params.id], type: QueryTypes.DELETE }
      );
      res.json({ message: 'Expertise removed' });
    } else {
      notFound(res, 'Expertise not found');
    }
  } catch (error) {
    serverError(res, error);
  }
};

module.exports = { getAllExpertises, createExpertise, updateExpertise, deleteExpertise };
