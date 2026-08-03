const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');
const slugify = require('slugify');

const Campaign = sequelize.define('Campaign', {
  id: {
    type: DataTypes.UUID,
    defaultValue: DataTypes.UUIDV4,
    primaryKey: true,
  },
  title: {
    type: DataTypes.STRING,
    allowNull: false,
  },
  slug: {
    type: DataTypes.STRING,
    unique: true,
  },
  content: {
    type: DataTypes.TEXT,
    allowNull: false,
  },
  image_url: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  category: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  is_published: {
    type: DataTypes.BOOLEAN,
    defaultValue: false,
  },
}, {
  tableName: 'campaigns',
  indexes: [
    {
      fields: ['is_published', 'createdAt'],
    },
    {
      fields: ['slug'],
    },
    {
      fields: ['category'],
    },
  ],
  hooks: {
    beforeValidate: (campaign) => {
      if (campaign.title) {
        campaign.slug = slugify(campaign.title, { lower: true, strict: true });
      }
    },
  },
});

module.exports = Campaign;
