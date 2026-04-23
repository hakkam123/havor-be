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

router.post('/', protect, upload.single('media_url'), upsertBanner);
router.put('/:id', protect, upload.single('media_url'), upsertBanner);
router.delete('/:id', protect, deleteBanner);

module.exports = router;
