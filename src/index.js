const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const path = require('path');
require('dotenv').config();

const { connectDB, sequelize } = require('./config/database');
const { errorHandler } = require('./middlewares/errorMiddleware');

// Import Routes
const authRoutes = require('./routes/authRoutes');
const newsRoutes = require('./routes/newsRoutes');
const bannerRoutes = require('./routes/bannerRoutes');
const expertiseRoutes = require('./routes/expertiseRoutes');
const categoryRoutes = require('./routes/categoryRoutes');
const productRoutes = require('./routes/productRoutes');
const workRoutes = require('./routes/workRoutes');
const contactRoutes = require('./routes/contactRoutes');

// Initialize App
const app = express();

// Connect Database
connectDB();

// Middlewares
app.use(helmet({
  crossOriginResourcePolicy: false, // Allow local images to be served
}));
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(morgan('dev'));

// Static Folders
app.use('/uploads', express.static(path.join(__dirname, '../uploads')));

// Routes
app.use('/api/auth', authRoutes);
app.use('/api/news', newsRoutes);
app.use('/api/banners', bannerRoutes);
app.use('/api/expertise', expertiseRoutes);
app.use('/api/categories', categoryRoutes);
app.use('/api/products', productRoutes);
app.use('/api/works', workRoutes);
app.use('/api/contact', contactRoutes);

// Health Check
app.get('/', (req, res) => {
  res.send('PT Havor Smarta Technology API is running...');
});

// Error Handler
app.use(errorHandler);

const PORT = process.env.PORT || 5000;

// Sync Database & Start Server
const startServer = async () => {
  try {
    // Note: use { alter: true } only in development to sync schema changes
    await sequelize.sync({ alter: true });
    console.log('✅ Database synchronized.');
    
    app.listen(PORT, () => {
      console.log(`🚀 Server running in ${process.env.NODE_ENV} mode on port ${PORT}`);
    });
  } catch (error) {
    console.error('❌ Error starting server:', error.message);
  }
};

startServer();
