const express = require('express');
const router = express.Router();
const { getAllProducts, createProduct, updateProduct, deleteProduct } = require('../controllers/productController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getAllProducts);
router.post('/', protect, upload.single('image_url'), validate(schemas.product.create), createProduct);
router.put('/:id', protect, validate(schemas.idParam, 'params'), upload.single('image_url'), validate(schemas.product.update), updateProduct);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteProduct);

module.exports = router;
