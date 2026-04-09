const { sequelize, connectDB } = require('./config/database');
const Admin = require('./models/Admin');
require('dotenv').config();

const seedAdmin = async () => {
  try {
    await connectDB();
    await sequelize.sync();

    const adminExists = await Admin.findOne({ where: { username: 'admin' } });

    if (adminExists) {
      console.log('Admin already exists.');
    } else {
      await Admin.create({
        username: 'admin',
        email: 'admin@havor.com',
        password: 'admin123admin' // In real life, should be changed immediately
      });
      console.log('✅ Default Admin created:');
      console.log('Username: admin');
      console.log('Password: admin123admin');
    }

    process.exit();
  } catch (error) {
    console.error('❌ Error seeding admin:', error.message);
    process.exit(1);
  }
};

seedAdmin();
