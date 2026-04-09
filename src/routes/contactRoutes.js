const express = require('express');
const router = express.Router();
const { 
  submitMessage, 
  getMessages, 
  markAsRead, 
  deleteMessage 
} = require('../controllers/contactController');
const { protect } = require('../middlewares/authMiddleware');

router.post('/', submitMessage);

// Protected routes for Admin CMS
router.get('/', protect, getMessages);
router.put('/:id/read', protect, markAsRead);
router.delete('/:id', protect, deleteMessage);

module.exports = router;
