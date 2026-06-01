const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');

const CareerApplication = sequelize.define('CareerApplication', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  full_name: {
    type: DataTypes.STRING,
    allowNull: false,
  },
  email: {
    type: DataTypes.STRING,
    allowNull: false,
    validate: {
      isEmail: true,
    },
  },
  phone: {
    type: DataTypes.STRING,
    allowNull: false,
  },
  address: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  position: {
    type: DataTypes.STRING,
    allowNull: false,
  },
  latest_education: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  experience_summary: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  portfolio_url: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  message: {
    type: DataTypes.TEXT,
    allowNull: false,
  },
  cv_original_name: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  cv_mime_type: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  cv_size: {
    type: DataTypes.INTEGER,
    allowNull: true,
  },
  cv_storage_key: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  cv_bucket: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  cv_url: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  cv_signed_url_strategy: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  status: {
    type: DataTypes.STRING,
    allowNull: false,
    defaultValue: 'new',
  },
}, {
  tableName: 'career_applications',
});

module.exports = CareerApplication;
