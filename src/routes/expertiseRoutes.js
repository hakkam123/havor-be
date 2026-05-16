const express = require('express');
const router = express.Router();
const { getAllExpertises, createExpertise, updateExpertise, deleteExpertise } = require('../controllers/expertiseController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getAllExpertises);
router.post('/', protect, upload.single('icon_url'), validate(schemas.expertise.create), createExpertise);
router.put('/:id', protect, validate(schemas.idParam, 'params'), upload.single('icon_url'), validate(schemas.expertise.update), updateExpertise);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteExpertise);

module.exports = router;
