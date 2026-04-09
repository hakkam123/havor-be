const express = require('express');
const router = express.Router();
const { getAllProducts, createProduct, updateProduct, deleteProduct } = require('../controllers/productController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');

router.get('/', getAllProducts);
router.post('/', protect, upload.single('image_url'), createProduct);
router.put('/:id', protect, upload.single('image_url'), updateProduct);
router.delete('/:id', protect, deleteProduct);

module.exports = router;
