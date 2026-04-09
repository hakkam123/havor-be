const express = require('express');
const router = express.Router();
const { getAllWorks, createWork, updateWork, deleteWork } = require('../controllers/workController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');

router.get('/', getAllWorks);
router.post('/', protect, upload.single('image_url'), createWork);
router.put('/:id', protect, upload.single('image_url'), updateWork);
router.delete('/:id', protect, deleteWork);

module.exports = router;
