const express = require('express');
const router = express.Router();
const { login, refresh, logout } = require('../controllers/authController');
const { createRateLimiter, validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

const loginRateLimiter = createRateLimiter({
  windowMs: 15 * 60 * 1000,
  max: 3,
  message: 'Too many authentication attempts. Please try again later.',
});

const refreshRateLimiter = createRateLimiter({
  windowMs: 15 * 60 * 1000,
  max: 30,
  message: 'Too many refresh attempts. Please try again later.',
});

router.post('/login', loginRateLimiter, validate(schemas.auth.login), login);
router.post('/refresh', refreshRateLimiter, validate(schemas.auth.refresh), refresh);
router.post('/logout', validate(schemas.auth.refresh), logout);

module.exports = router;
