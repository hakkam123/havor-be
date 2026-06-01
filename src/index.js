const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const path = require('path');
require('dotenv').config();

const { connectDB, sequelize } = require('./config/database');
const { errorHandler } = require('./middlewares/errorMiddleware');

require('./models/Admin');
require('./models/AdminSession');
require('./models/Career');
require('./models/CareerApplication');
require('./models/Category');
require('./models/Client');
require('./models/CompanyProfile');
require('./models/ContactMessage');
require('./models/Expertise');
require('./models/HeroBanner');
require('./models/News');
require('./models/Product');
require('./models/Work');

const authRoutes = require('./routes/authRoutes');
const newsRoutes = require('./routes/newsRoutes');
const bannerRoutes = require('./routes/bannerRoutes');
const expertiseRoutes = require('./routes/expertiseRoutes');
const categoryRoutes = require('./routes/categoryRoutes');
const clientRoutes = require('./routes/clientRoutes');
const careerRoutes = require('./routes/careerRoutes');
const productRoutes = require('./routes/productRoutes');
const workRoutes = require('./routes/workRoutes');
const contactRoutes = require('./routes/contactRoutes');
const profileRoutes = require('./routes/profileRoutes');

const app = express();

connectDB();

const configuredOrigins = String(process.env.ALLOWED_ORIGINS || '')
  .split(',')
  .map((origin) => origin.trim())
  .filter(Boolean);

const allowedOrigins = [
  'http://127.0.0.1:5173',
  'http://localhost:5173',
  'http://127.0.0.1:3000',
  'http://localhost:3000',
  'http://localhost:3005',
  'https://havorsmarta.netlify.app',
  'https://admin.havor.com',
  process.env.FRONTEND_URL,
  process.env.APP_URL,
  ...configuredOrigins,
].filter(Boolean);

app.use(cors({
  origin: (origin, callback) => {
    if (!origin || allowedOrigins.includes(origin)) {
      return callback(null, true);
    }

    callback(new Error('Not allowed by CORS'));
  },
  credentials: true,
}));

app.use(helmet({
  crossOriginResourcePolicy: false,
}));
app.use(express.json({ limit: '1mb' }));
app.use(express.urlencoded({ extended: true }));
app.use(morgan(process.env.NODE_ENV === 'production' ? 'combined' : 'dev'));

app.use('/uploads', express.static(path.join(__dirname, '../uploads'), {
  dotfiles: 'deny',
  index: false,
}));

app.use('/api/auth', authRoutes);
app.use('/api/news', newsRoutes);
app.use('/api/banners', bannerRoutes);
app.use('/api/expertise', expertiseRoutes);
app.use('/api/categories', categoryRoutes);
app.use('/api/clients', clientRoutes);
app.use('/api/careers', careerRoutes);
app.use('/api/products', productRoutes);
app.use('/api/works', workRoutes);
app.use('/api/contact', contactRoutes);
app.use('/api/profile', profileRoutes);

app.get('/', (req, res) => {
  res.send('PT Havor Smarta Technology API is running...');
});

app.use(errorHandler);

const PORT = process.env.PORT || 5000;

const startServer = async () => {
  try {
    const syncOptions = process.env.NODE_ENV === 'production' ? {} : { alter: true };
    await sequelize.sync(syncOptions);
    console.log('Database synchronized.');

    app.listen(PORT, () => {
      console.log(`Server running in ${process.env.NODE_ENV} mode on port ${PORT}`);
    });
  } catch (error) {
    console.error('Error starting server:', error.message);
  }
};

startServer();
