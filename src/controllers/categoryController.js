const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const {
  conflictError,
  isDuplicateEntry,
  notFound,
  serverError,
} = require('../utils/apiResponse');

// @desc    Get all categories
// @route   GET /api/categories
// @access  Public
const getAllCategories = async (req, res) => {
  try {
    const categories = await sequelize.query(
      'SELECT * FROM categories ORDER BY name ASC',
      { type: QueryTypes.SELECT }
    );
    res.json(categories);
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Create category
// @route   POST /api/categories
// @access  Private/Admin
const createCategory = async (req, res) => {
  const { name } = req.body;
  try {
    const result = await sequelize.query(
      'INSERT INTO categories (name, createdAt, updatedAt) VALUES (?, NOW(), NOW())',
      {
        replacements: [name],
        type: QueryTypes.INSERT
      }
    );
    res.status(201).json({ id: result[0], name });
  } catch (error) {
    if (isDuplicateEntry(error)) {
      return conflictError(res, 'name', 'Category name already exists');
    }
    serverError(res, error);
  }
};

// @desc    Update category
// @route   PUT /api/categories/:id
// @access  Private/Admin
const updateCategory = async (req, res) => {
  const { name } = req.body;
  const { id } = req.params;
  try {
    const existing = await sequelize.query(
      'SELECT id FROM categories WHERE id = ?',
      { replacements: [id], type: QueryTypes.SELECT }
    );
    if (existing.length === 0) {
      return notFound(res, 'Category not found');
    }

    await sequelize.query(
      'UPDATE categories SET name = ?, updatedAt = NOW() WHERE id = ?',
      {
        replacements: [name, id],
        type: QueryTypes.UPDATE
      }
    );
    res.json({ message : 'Category updated successfully' });
  } catch (error) {
    if (isDuplicateEntry(error)) {
      return conflictError(res, 'name', 'Category name already exists');
    }
    serverError(res, error);
  }
};

// @desc    Delete category
// @route   DELETE /api/categories/:id
// @access  Private/Admin
const deleteCategory = async (req, res) => {
  try {
    const existing = await sequelize.query(
      'SELECT id FROM categories WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );
    if (existing.length === 0) {
      return notFound(res, 'Category not found');
    }

    await sequelize.query(
      'DELETE FROM categories WHERE id = ?',
      {
        replacements: [req.params.id],
        type: QueryTypes.DELETE
      }
    );
    res.json({ message: 'Category removed' });
  } catch (error) {
    serverError(res, error);
  }
};

module.exports = {
  getAllCategories,
  createCategory,
  updateCategory,
  deleteCategory
};
