const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const {
  conflictError,
  isDuplicateEntry,
  notFound,
  relationError,
  serverError,
} = require('../utils/apiResponse');
const { createId } = require('../utils/id');
const { getPagination, sendListResponse } = require('../utils/pagination');

const categoryTypes = ['News', 'Career', 'Campaign', 'Product'];

const normalizeType = (type) => {
  const normalizedType = String(type || 'Product').trim().toLowerCase();
  return categoryTypes.find((item) => item.toLowerCase() === normalizedType) || 'Product';
};

// @desc    Get all categories
// @route   GET /api/categories
// @access  Public
const getAllCategories = async (req, res) => {
  try {
    const pagination = getPagination(req.query);
    const type = req.query.type ? normalizeType(req.query.type) : null;
    const search = String(req.query.search || '').trim();
    const where = [];
    const replacements = [];

    if (type) {
      where.push('type = ?');
      replacements.push(type);
    }

    if (search) {
      const keyword = `%${search}%`;
      where.push('(name LIKE ? OR type LIKE ?)');
      replacements.push(keyword, keyword);
    }

    const whereClause = where.length ? `WHERE ${where.join(' AND ')}` : '';
    const countResult = pagination
      ? await sequelize.query(`SELECT COUNT(*) AS total FROM categories ${whereClause}`, { replacements, type: QueryTypes.SELECT })
      : [{ total: 0 }];
    const dataReplacements = [...replacements];
    const paginationSql = pagination ? ' LIMIT ? OFFSET ?' : '';
    if (pagination) dataReplacements.push(pagination.limit, pagination.offset);
    const categories = await sequelize.query(
      `SELECT * FROM categories ${whereClause} ORDER BY type ASC, name ASC${paginationSql}`,
      { replacements: dataReplacements, type: QueryTypes.SELECT }
    );
    sendListResponse(res, categories, pagination, countResult[0]?.total);
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Create category
// @route   POST /api/categories
// @access  Private/Admin
const createCategory = async (req, res) => {
  const { name } = req.body;
  const type = normalizeType(req.body.type);
  try {
    const id = createId();
    await sequelize.query(
      'INSERT INTO categories (id, name, type, createdAt, updatedAt) VALUES (?, ?, ?, NOW(), NOW())',
      {
        replacements: [id, name, type],
        type: QueryTypes.INSERT
      }
    );
    res.status(201).json({ id, name, type });
  } catch (error) {
    if (isDuplicateEntry(error)) {
      return conflictError(res, 'name', 'Category name already exists for this type');
    }
    serverError(res, error);
  }
};

// @desc    Update category
// @route   PUT /api/categories/:id
// @access  Private/Admin
const updateCategory = async (req, res) => {
  const { name } = req.body;
  const type = normalizeType(req.body.type);
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
      'UPDATE categories SET name = ?, type = ?, updatedAt = NOW() WHERE id = ?',
      {
        replacements: [name, type, id],
        type: QueryTypes.UPDATE
      }
    );
    res.json({ id, name, type, message : 'Category updated successfully' });
  } catch (error) {
    if (isDuplicateEntry(error)) {
      return conflictError(res, 'name', 'Category name already exists for this type');
    }
    serverError(res, error);
  }
};

// @desc    Delete category
// @route   DELETE /api/categories/:id
// @access  Private/Admin
const deleteCategory = async (req, res) => {
  try {
    const categories = await sequelize.query(
      'SELECT id, name, type FROM categories WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );
    if (categories.length === 0) {
      return notFound(res, 'Category not found');
    }

    const category = categories[0];
    const usage = await sequelize.query(
      `SELECT
        (SELECT COUNT(*) FROM products WHERE categoryId = ?) AS productCount,
        (SELECT COUNT(*) FROM news WHERE category = ?) AS newsCount,
        (SELECT COUNT(*) FROM campaigns WHERE category = ?) AS campaignCount,
        (SELECT COUNT(*) FROM careers WHERE categoryId = ?) AS careerCount`,
      {
        replacements: [category.id, category.name, category.name, category.id],
        type: QueryTypes.SELECT
      }
    );
    const counts = usage[0] || {};
    const usedBy = [
      Number(counts.productCount) > 0 ? 'products' : '',
      Number(counts.newsCount) > 0 ? 'news' : '',
      Number(counts.campaignCount) > 0 ? 'campaigns' : '',
      Number(counts.careerCount) > 0 ? 'careers' : '',
    ].filter(Boolean);

    if (usedBy.length) {
      return relationError(
        res,
        'category',
        `This category is still used by ${usedBy.join(', ')}. Move the related content before deleting it.`
      );
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
