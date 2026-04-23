const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');

const HeroBanner = sequelize.define('HeroBanner', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  page_name: {
    type: DataTypes.STRING,
    allowNull: false,
    unique: true, 
  },
  title: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  subtitle: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  media_url: {
    type: DataTypes.STRING,
    allowNull: false,
  },
  media_type: {
    type: DataTypes.ENUM('image', 'video'),
    defaultValue: 'image',
  },
}, {
  tableName: 'hero_banners',
});

module.exports = HeroBanner;
