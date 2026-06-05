const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');

const Category = sequelize.define('Category', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  name: {
    type: DataTypes.STRING,
    allowNull: false,
  },
  type: {
    type: DataTypes.ENUM('News', 'Career', 'Campaign', 'Product'),
    allowNull: false,
    defaultValue: 'Product',
  },
}, {
  tableName: 'categories',
  indexes: [
    {
      unique: true,
      fields: ['type', 'name'],
    },
    {
      fields: ['type'],
    },
  ],
});

module.exports = Category;
