const express = require('express');
const router = express.Router();
const { 
  getAllBanners, 
  getBannerByPage, 
  upsertBanner, 
  deleteBanner 
} = require('../controllers/bannerController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');

router.get('/', getAllBanners);
router.get('/:page', getBannerByPage);

// Protected routes
router.post('/', protect, upload.single('media'), upsertBanner);
router.delete('/:id', protect, deleteBanner);

module.exports = router;
