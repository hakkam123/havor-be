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

router.get('/', getAllCareers);


// Protected routes
router.post('/', protect, upload.single('thumbnail'), createCareer);
router.put('/:id', protect, upload.single('thumbnail'), updateCareer);
router.delete('/:id', protect, deleteCareer);

module.exports = router;
