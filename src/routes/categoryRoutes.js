const express = require('express');
const router = express.Router();
const { 
  getAllCategories, 
  createCategory, 
  updateCategory, 
  deleteCategory 
} = require('../controllers/categoryController');
const { protect } = require('../middlewares/authMiddleware');
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getAllCategories);
router.post('/', protect, validate(schemas.category.create), createCategory);
router.put('/:id', protect, validate(schemas.idParam, 'params'), validate(schemas.category.update), updateCategory);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteCategory);

module.exports = router;
