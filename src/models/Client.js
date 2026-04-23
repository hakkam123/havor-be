const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');    

const Client = sequelize.define('Client', {
    id: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true,
    },
    name: {
        type: DataTypes.STRING,
        allowNull: false,
    },
    client_icon: {
        type: DataTypes.STRING,
        allowNull: true,
    },
    description: {
        type: DataTypes.STRING,
        allowNull: false,
    }
}, {
    tableName: 'clients',
}
);

module.exports = Client;