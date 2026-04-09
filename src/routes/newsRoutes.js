const express = require('express');
const router = express.Router();
const { 
  getAllNews, 
  getNewsBySlug, 
  createNews, 
  updateNews, 
  deleteNews 
} = require('../controllers/newsController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');

router.get('/', getAllNews);
router.get('/:slug', getNewsBySlug);

// Protected routes
router.post('/', protect, upload.single('image'), createNews);
router.put('/:id', protect, upload.single('image'), updateNews);
router.delete('/:id', protect, deleteNews);

module.exports = router;
