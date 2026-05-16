const express = require('express');
const router = express.Router();
const {
getAllCareers,
    createCareer,
    updateCareer,
    deleteCareer
} = require('../controllers/careerController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getAllCareers);


// Protected routes
router.post('/', protect, upload.single('thumbnail'), validate(schemas.career.create), createCareer);
router.put('/:id', protect, validate(schemas.idParam, 'params'), upload.single('thumbnail'), validate(schemas.career.update), updateCareer);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteCareer);

module.exports = router;
