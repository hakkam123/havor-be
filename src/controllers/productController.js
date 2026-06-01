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
    `SELECT id FROM products WHERE ${condition} LIMIT 1`,
    { replacements, type: QueryTypes.SELECT }
  );
  return existing.length === 0;
};

// @desc    Get all products
// @route   GET /api/products
// @access  Public
const getAllProducts = async (req, res) => {
  try {
    const products = await sequelize.query(
      `SELECT p.*, c.name as category_name 
       FROM products p 
       LEFT JOIN categories c ON p.categoryId = c.id 
       ORDER BY p.createdAt DESC`,
      { type: QueryTypes.SELECT }
    );
    res.json(products);
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Create product
// @route   POST /api/products
// @access  Private/Admin
const createProduct = async (req, res) => {
  const { name, description, external_link, categoryId } = req.body;
  const image_url = req.file ? `/uploads/products/${req.file.filename}` : null;
  try {
    if (!(await ensureUniqueName(name))) {
      cleanupUploadedFile(req);
      return conflictError(res, 'name', 'Product name already exists');
    }

    const [result] = await sequelize.query(
      `INSERT INTO products (name, description, image_url, external_link, categoryId, createdAt, updatedAt) 
       VALUES (?, ?, ?, ?, ?, NOW(), NOW())`,
      {
        replacements: [name, description, image_url, external_link, categoryId || null],
        type: QueryTypes.INSERT
      }
    );
    res.status(201).json({ id: result, name, description, image_url, external_link, categoryId });
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

// @desc    Update product
// @route   PUT /api/products/:id
// @access  Private/Admin
const updateProduct = async (req, res) => {
  try {
    const products = await sequelize.query(
      'SELECT * FROM products WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );
    
    if (products.length > 0) {
      const product = products[0];
      const { name, description, external_link, categoryId } = req.body;
      let image_url = product.image_url;

      if (name && !(await ensureUniqueName(name, req.params.id))) {
        cleanupUploadedFile(req);
        return conflictError(res, 'name', 'Product name already exists');
      }

      if (req.file) {
        removeFile(product.image_url);
        image_url = `/uploads/products/${req.file.filename}`;
      }

      await sequelize.query(
        `UPDATE products SET 
          name = ?, 
          description = ?, 
          image_url = ?, 
          external_link = ?, 
          categoryId = ?, 
          updatedAt = NOW() 
         WHERE id = ?`,
        {
          replacements: [
            name || product.name,
            description || product.description,
            image_url,
            external_link || product.external_link,
            categoryId !== undefined ? categoryId : product.categoryId,
            req.params.id
          ],
          type: QueryTypes.UPDATE
        }
      );
      res.json({ id: req.params.id, name, description, image_url, categoryId });
    } else {
      cleanupUploadedFile(req);
      notFound(res, 'Product not found');
    }
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

// @desc    Delete product
// @route   DELETE /api/products/:id
// @access  Private/Admin
const deleteProduct = async (req, res) => {
  try {
    const products = await sequelize.query(
      'SELECT * FROM products WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );

    if (products.length > 0) {
      const product = products[0];
      removeFile(product.image_url);
      await sequelize.query(
        'DELETE FROM products WHERE id = ?',
        { replacements: [req.params.id], type: QueryTypes.DELETE }
      );
      res.json({ message: 'Product removed' });
    } else {
      notFound(res, 'Product not found');
    }
  } catch (error) {
    serverError(res, error);
  }
};

module.exports = { getAllProducts, createProduct, updateProduct, deleteProduct };
