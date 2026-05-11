const crypto = require('crypto');
const jwt = require('jsonwebtoken');
const Admin = require('../models/Admin');
const AdminSession = require('../models/AdminSession');

const hashToken = (token) => {
  return crypto.createHash('sha256').update(token).digest('hex');
};

const getRefreshTokenExpiresAt = (refreshToken) => {
  const decodedToken = jwt.decode(refreshToken);

  if (!decodedToken?.exp) {
    throw new Error('Failed to determine refresh token expiry');
  }

  return new Date(decodedToken.exp * 1000);
};

const getIpAddress = (req) => {
  const forwardedFor = req.headers['x-forwarded-for'];

  if (typeof forwardedFor === 'string' && forwardedFor.trim()) {
    return forwardedFor.split(',')[0].trim();
  }

  return req.ip || null;
};

const createAdminSession = async (adminId, refreshToken, req) => {
  return AdminSession.create({
    adminId,
    tokenHash: hashToken(refreshToken),
    expiresAt: getRefreshTokenExpiresAt(refreshToken),
    ipAddress: getIpAddress(req),
    userAgent: req.get('user-agent') || null,
  });
};

// @desc    Auth admin & get token
// @route   POST /api/auth/login
// @access  Public
const login = async (req, res) => {
  const { email, password } = req.body;

  try {
    const admin = await Admin.findOne({ where: { email } });

    if (!admin || !(await admin.comparePassword(password))) {
      return res.status(401).json({ message: 'Invalid email or password' });
    }

    const accessToken = generateAccessToken(admin.id);
    const refreshToken = generateRefreshToken(admin.id);

    await createAdminSession(admin.id, refreshToken, req);

    res.json({
      id: admin.id,
      username: admin.username,
      email: admin.email,
      accessToken,
      refreshToken,
    });
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// @desc    Refresh access token
// @route   POST /api/auth/refresh
// @access  Public
const refresh = async (req, res) => {
  const { refreshToken } = req.body;

  if (!refreshToken) {
    return res.status(400).json({ message: 'Refresh token is required' });
  }

  try {
    const decoded = jwt.verify(refreshToken, process.env.JWT_REFRESH_SECRET);
    const session = await AdminSession.findOne({
      where: {
        adminId: decoded.id,
        tokenHash: hashToken(refreshToken),
        revokedAt: null,
      },
    });

    if (!session || session.expiresAt <= new Date()) {
      return res.status(401).json({ message: 'Invalid or expired refresh token' });
    }

    const now = new Date();

    const [revokedSessionCount] = await AdminSession.update({
      revokedAt: now,
      lastUsedAt: now,
    }, {
      where: {
        id: session.id,
        revokedAt: null,
      },
    });

    if (revokedSessionCount !== 1) {
      return res.status(401).json({ message: 'Invalid or expired refresh token' });
    }

    const newAccessToken = generateAccessToken(decoded.id);
    const newRefreshToken = generateRefreshToken(decoded.id);

    await createAdminSession(decoded.id, newRefreshToken, req);

    res.json({
      accessToken: newAccessToken,
      refreshToken: newRefreshToken,
    });
  } catch (error) {
    res.status(401).json({ message: 'Invalid or expired refresh token' });
  }
};

// @desc    Logout admin session
// @route   POST /api/auth/logout
// @access  Public
const logout = async (req, res) => {
  const { refreshToken } = req.body;

  if (!refreshToken) {
    return res.status(400).json({ message: 'Refresh token is required' });
  }

  try {
    const session = await AdminSession.findOne({
      where: {
        tokenHash: hashToken(refreshToken),
        revokedAt: null,
      },
    });

    if (session) {
      const now = new Date();

      await session.update({
        revokedAt: now,
        lastUsedAt: now,
      });
    }

    res.json({ message: 'Logged out successfully' });
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// Generate Access Token (Short-lived)
const generateAccessToken = (id) => {
  return jwt.sign({ id }, process.env.JWT_SECRET, {
    expiresIn: process.env.JWT_EXPIRES_IN || '15m',
  });
};

// Generate Refresh Token (Longer-lived)
const generateRefreshToken = (id) => {
  return jwt.sign({ id, jti: crypto.randomUUID() }, process.env.JWT_REFRESH_SECRET, {
    expiresIn: process.env.JWT_REFRESH_EXPIRES_IN || '120m',
  });
};

module.exports = { login, refresh, logout };
