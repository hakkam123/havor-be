const express = require('express');
const router = express.Router();
const { 
  submitMessage, 
  getMessages, 
  markAsRead, 
  deleteMessage 
} = require('../controllers/contactController');
const { protect } = require('../middlewares/authMiddleware');
const { createRateLimiter, validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

const contactRateLimiter = createRateLimiter({
  windowMs: 15 * 60 * 1000,
  max: 10,
  message: 'Too many contact submissions. Please try again later.',
});

router.post('/', contactRateLimiter, validate(schemas.contact.submit), submitMessage);

// Protected routes for Admin CMS
router.get('/', protect, getMessages);
router.put('/:id/read', protect, validate(schemas.idParam, 'params'), markAsRead);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteMessage);

module.exports = router;
