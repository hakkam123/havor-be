const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const {
  cleanupUploadedFile,
  conflictError,
  notFound,
  removeFile,
  serverError,
} = require('../utils/apiResponse');

const ensureUniqueTitle = async (title, ignoreId = null) => {
  const replacements = ignoreId ? [title, ignoreId] : [title];
  const condition = ignoreId ? 'LOWER(title) = LOWER(?) AND id != ?' : 'LOWER(title) = LOWER(?)';
  const existing = await sequelize.query(
    `SELECT id FROM works WHERE ${condition} LIMIT 1`,
    { replacements, type: QueryTypes.SELECT }
  );
  return existing.length === 0;
};

// @desc    Get all works
// @route   GET /api/works
// @access  Public
const getAllWorks = async (req, res) => {
  try {
    const data = await sequelize.query(
      `SELECT w.*, c.name as category_name 
       FROM works w 
       LEFT JOIN categories c ON w.categoryId = c.id 
       ORDER BY w.createdAt DESC`,
      { type: QueryTypes.SELECT }
    );
    res.json(data);
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Create work
// @route   POST /api/works
// @access  Private/Admin
const createWork = async (req, res) => {
  const { title, description, client, year, categoryId } = req.body;
  const image_url = req.file ? `/uploads/works/${req.file.filename}` : null;
  try {
    if (!(await ensureUniqueTitle(title))) {
      cleanupUploadedFile(req);
      return conflictError(res, 'title', 'Work title already exists');
    }

    const [result] = await sequelize.query(
      `INSERT INTO works (title, description, client, year, image_url, categoryId, createdAt, updatedAt) 
       VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())`,
      {
        replacements: [title, description, client, year, image_url, categoryId || null],
        type: QueryTypes.INSERT
      }
    );
    res.status(201).json({ id: result, title, description, client, year, image_url, categoryId });
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

// @desc    Update work
// @route   PUT /api/works/:id
// @access  Private/Admin
const updateWork = async (req, res) => {
  try {
    const works = await sequelize.query(
      'SELECT * FROM works WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );
    
    if (works.length > 0) {
      const work = works[0];
      const { title, description, client, year, categoryId } = req.body;
      let image_url = work.image_url;

      if (title && !(await ensureUniqueTitle(title, req.params.id))) {
        cleanupUploadedFile(req);
        return conflictError(res, 'title', 'Work title already exists');
      }

      if (req.file) {
        removeFile(work.image_url);
        image_url = `/uploads/works/${req.file.filename}`;
      }

      await sequelize.query(
        `UPDATE works SET 
          title = ?, 
          description = ?, 
          client = ?, 
          year = ?, 
          image_url = ?, 
          categoryId = ?, 
          updatedAt = NOW() 
         WHERE id = ?`,
        {
          replacements: [
            title || work.title,
            description || work.description,
            client || work.client,
            year || work.year,
            image_url,
            categoryId !== undefined ? categoryId : work.categoryId,
            req.params.id
          ],
          type: QueryTypes.UPDATE
        }
      );
      res.json({ id: req.params.id, title, description, client, year, image_url, categoryId });
    } else {
      cleanupUploadedFile(req);
      notFound(res, 'Work entry not found');
    }
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

// @desc    Delete work
// @route   DELETE /api/works/:id
// @access  Private/Admin
const deleteWork = async (req, res) => {
  try {
    const works = await sequelize.query(
      'SELECT * FROM works WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );

    if (works.length > 0) {
      const work = works[0];
      removeFile(work.image_url);
      await sequelize.query(
        'DELETE FROM works WHERE id = ?',
        { replacements: [req.params.id], type: QueryTypes.DELETE }
      );
      res.json({ message: 'Work entry removed' });
    } else {
      notFound(res, 'Work entry not found');
    }
  } catch (error) {
    serverError(res, error);
  }
};

module.exports = { getAllWorks, createWork, updateWork, deleteWork };
