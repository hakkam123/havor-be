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
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getAllNews);
router.get('/:slug', getNewsBySlug);

// Protected routes
router.post('/', protect, upload.single('image_url'), validate(schemas.news.create), createNews);
router.put('/:id', protect, validate(schemas.idParam, 'params'), upload.single('image_url'), validate(schemas.news.update), updateNews);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteNews);

module.exports = router;
