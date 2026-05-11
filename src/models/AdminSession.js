const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');
const Admin = require('./Admin');

const AdminSession = sequelize.define('AdminSession', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  adminId: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: 'admins',
      key: 'id',
    },
    onUpdate: 'CASCADE',
    onDelete: 'CASCADE',
  },
  tokenHash: {
    type: DataTypes.STRING(64),
    allowNull: false,
    unique: true,
  },
  expiresAt: {
    type: DataTypes.DATE,
    allowNull: false,
  },
  revokedAt: {
    type: DataTypes.DATE,
    allowNull: true,
  },
  lastUsedAt: {
    type: DataTypes.DATE,
    allowNull: true,
  },
  ipAddress: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  userAgent: {
    type: DataTypes.STRING,
    allowNull: true,
  },
}, {
  tableName: 'admin_sessions',
  indexes: [
    { fields: ['adminId'] },
    { fields: ['expiresAt'] },
    { fields: ['revokedAt'] },
  ],
});

Admin.hasMany(AdminSession, {
  foreignKey: 'adminId',
  as: 'sessions',
});

AdminSession.belongsTo(Admin, {
  foreignKey: 'adminId',
  as: 'admin',
});

module.exports = AdminSession;
