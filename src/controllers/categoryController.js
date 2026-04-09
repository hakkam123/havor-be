const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');

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
    res.status(500).json({ message: error.message });
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
    res.status(500).json({ message: error.message });
  }
};

// @desc    Update category
// @route   PUT /api/categories/:id
// @access  Private/Admin
const updateCategory = async (req, res) => {
  const { name } = req.body;
  try {
    await sequelize.query(
      'UPDATE categories SET name = ?, updatedAt = NOW() WHERE id = ?',
      {
        replacements: [name, req.params.id],
        type: QueryTypes.UPDATE
      }
    );
    res.json({ id: req.params.id, name });
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// @desc    Delete category
// @route   DELETE /api/categories/:id
// @access  Private/Admin
const deleteCategory = async (req, res) => {
  try {
    await sequelize.query(
      'DELETE FROM categories WHERE id = ?',
      {
        replacements: [req.params.id],
        type: QueryTypes.DELETE
      }
    );
    res.json({ message: 'Category removed' });
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

module.exports = {
  getAllCategories,
  createCategory,
  updateCategory,
  deleteCategory
};
