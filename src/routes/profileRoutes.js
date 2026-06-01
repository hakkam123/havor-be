const express = require('express');
const router = express.Router();
const { getProfile, updateProfile } = require('../controllers/profileController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getProfile);
router.put('/', protect, upload.single('logo_url'), validate(schemas.profile.update), updateProfile);

module.exports = router;
