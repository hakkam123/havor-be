const Joi = require('joi');

const validate = (schema, property = 'body') => {
  return (req, res, next) => {
    const { error, value } = schema.validate(req[property], {
      abortEarly: false,
      stripUnknown: true,
      convert: true,
    });

    if (error) {
      return res.status(400).json({
        message: 'Invalid request data',
        errors: error.details.map((detail) => detail.message),
      });
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
      return res.status(429).json({ message });
    }

    next();
  };
};

module.exports = { Joi, validate, createRateLimiter };
