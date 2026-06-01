const jwt = require('jsonwebtoken');
const Admin = require('../models/Admin');

const protect = async (req, res, next) => {
  let token;

  if (
    req.headers.authorization &&
    req.headers.authorization.startsWith('Bearer')
  ) {
    try {
      // Get token from header
      token = req.headers.authorization.split(' ')[1];

      // Verify token
      const decoded = jwt.verify(token, process.env.JWT_SECRET);

      // Get admin from token
      req.admin = await Admin.findByPk(decoded.id, {
        attributes: { exclude: ['password'] }
      });

      if (!req.admin) {
        return res.status(401).json({ success: false, message: 'Not authorized, admin not found' });
      }

      next();
    } catch (error) {
      res.status(401).json({ success: false, message: 'Not authorized, token failed' });
    }
  }

  if (!token) {
    res.status(401).json({ success: false, message: 'Not authorized, no token' });
  }
};

const optionalAdmin = async (req, res, next) => {
  const authorization = req.headers.authorization;
  if (!authorization?.startsWith('Bearer')) return next();

  try {
    const token = authorization.split(' ')[1];
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    req.admin = await Admin.findByPk(decoded.id, {
      attributes: { exclude: ['password'] },
    });
  } catch (error) {
    req.admin = null;
  }

  next();
};

module.exports = { optionalAdmin, protect };
