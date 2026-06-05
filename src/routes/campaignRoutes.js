const express = require('express');
const router = express.Router();
const {
  getAllCampaigns,
  getCampaignBySlug,
  createCampaign,
  updateCampaign,
  deleteCampaign,
} = require('../controllers/campaignController');
const { optionalAdmin, protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', optionalAdmin, getAllCampaigns);
router.get('/:slug', optionalAdmin, getCampaignBySlug);
router.post('/', protect, upload.single('image_url'), validate(schemas.campaign.create), createCampaign);
router.put('/:id', protect, validate(schemas.idParam, 'params'), upload.single('image_url'), validate(schemas.campaign.update), updateCampaign);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteCampaign);

module.exports = router;
