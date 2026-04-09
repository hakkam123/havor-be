const express = require('express');
const router = express.Router();
const { getAllExpertises, createExpertise, updateExpertise, deleteExpertise } = require('../controllers/expertiseController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');

router.get('/', getAllExpertises);
router.post('/', protect, upload.single('icon_url'), createExpertise);
router.put('/:id', protect, upload.single('icon_url'), updateExpertise);
router.delete('/:id', protect, deleteExpertise);

module.exports = router;
