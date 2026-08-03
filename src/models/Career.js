const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');

const Career = sequelize.define('Career', {
    id: {
        type: DataTypes.UUID,
        defaultValue: DataTypes.UUIDV4,
        primaryKey: true,
    },
    thumbnail: {
        type: DataTypes.STRING,
        allowNull: true,
    },
    job_title: {
        type: DataTypes.STRING,
        allowNull: false,

    },
    job_description: {
        type: DataTypes.TEXT,
        allowNull: false
    },
    categoryId: {
        type: DataTypes.UUID,
        allowNull: true,
        references: {
            model: 'categories',
            key: 'id'
        }
    },
}, {
    tableName: 'careers',
});

module.exports = Career;
