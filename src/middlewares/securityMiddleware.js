const Joi = require('joi');
const { cleanupUploadedFile, toValidationErrors, validationError } = require('../utils/apiResponse');

const validate = (schema, property = 'body') => {
  return (req, res, next) => {
    const { error, value } = schema.validate(req[property], {
      abortEarly: false,
      stripUnknown: true,
      convert: true,
    });

    if (error) {
      cleanupUploadedFile(req);
      return validationError(res, toValidationErrors(error.details));
    }

    req[property] = value;
    next();
  };
};

const createRateLimiter = ({ windowMs, max, message }) => {
  const attempts = new Map();

  return (req, res, next) => {
    const key = req.ip || req.socket.remoteAddress || 'unknown';
    const now = Date.now();
    const current = attempts.get(key);

    if (attempts.size > 10000) {
      attempts.clear();
    }

    if (!current || current.resetAt <= now) {
      attempts.set(key, { count: 1, resetAt: now + windowMs });
      return next();
    }

    current.count += 1;

    if (current.count > max) {
      res.set('Retry-After', Math.ceil((current.resetAt - now) / 1000));
      return res.status(429).json({
        success: false,
        code: 'RATE_LIMITED',
        message,
      });
    }

    next();
  };
};

module.exports = { Joi, validate, createRateLimiter };
