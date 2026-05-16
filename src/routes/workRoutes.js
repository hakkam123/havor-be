const express = require('express');
const router = express.Router();
const { getAllWorks, createWork, updateWork, deleteWork } = require('../controllers/workController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getAllWorks);
router.post('/', protect, upload.single('image_url'), validate(schemas.work.create), createWork);
router.put('/:id', protect, validate(schemas.idParam, 'params'), upload.single('image_url'), validate(schemas.work.update), updateWork);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteWork);

module.exports = router;
