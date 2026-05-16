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
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getAllBanners);
router.get('/:page', getBannerByPage);

router.post('/', protect, upload.single('media_url'), validate(schemas.banner.upsert), upsertBanner);
router.put('/:id', protect, validate(schemas.idParam, 'params'), upload.single('media_url'), validate(schemas.banner.upsert), upsertBanner);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteBanner);

module.exports = router;
