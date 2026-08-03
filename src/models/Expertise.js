const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');

const Expertise = sequelize.define('Expertise', {
  id: {
    type: DataTypes.UUID,
    defaultValue: DataTypes.UUIDV4,
    primaryKey: true,
  },
  name: {
    type: DataTypes.STRING,
    allowNull: false,
  },
  description: {
    type: DataTypes.TEXT,
    allowNull: false,
  },
  icon_url: {
    type: DataTypes.STRING,
    allowNull: true,
  },
}, {
  tableName: 'expertises',
});

module.exports = Expertise;
