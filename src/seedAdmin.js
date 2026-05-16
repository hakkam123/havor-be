const { sequelize, connectDB } = require('./config/database');
const Admin = require('./models/Admin');
require('dotenv').config();

const seedAdmin = async () => {
  try {
    await connectDB();
    await sequelize.sync();

    const defaultPassword = process.env.ADMIN_PASSWORD || 'HavorAdmin@2026';
    const adminExists = await Admin.findOne({ where: { username: 'admin' } });

    if (adminExists) {
      console.log('Admin already exists.');
    } else {
      await Admin.create({
        username: 'admin',
        email: 'admin@havor.com',
        password: defaultPassword,
      });
      console.log('Default Admin created:');
      console.log('Username: admin');
      console.log('Password: use ADMIN_PASSWORD from .env or rotate it immediately after first login');
    }

    process.exit();
  } catch (error) {
    console.error('Error seeding admin:', error.message);
    process.exit(1);
  }
};

seedAdmin();
