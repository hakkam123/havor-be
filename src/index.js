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
require('./models/Campaign');
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
const campaignRoutes = require('./routes/campaignRoutes');
const productRoutes = require('./routes/productRoutes');
const workRoutes = require('./routes/workRoutes');
const contactRoutes = require('./routes/contactRoutes');
const profileRoutes = require('./routes/profileRoutes');

const app = express();
const appBasePath = String(process.env.APP_BASE_PATH || '/havor').replace(/\/+$/, '');
const uploadsPath = path.join(__dirname, '../uploads');

connectDB();

const normalizeOrigin = (origin) => String(origin || '').trim().replace(/\/+$/, '');

const configuredOrigins = String(process.env.ALLOWED_ORIGINS || '')
  .split(',')
  .map(normalizeOrigin)
  .filter(Boolean);

const allowedOrigins = [
  'http://127.0.0.1:5173',
  'http://localhost:5173',
  'http://127.0.0.1:3000',
  'http://localhost:3000',
  'http://localhost:3005',
  'https://havorsmarta.netlify.app',
  'https://admin.havor.com',
  normalizeOrigin(process.env.FRONTEND_URL),
  normalizeOrigin(process.env.APP_URL),
  ...configuredOrigins,
].filter(Boolean);

app.use(cors({
  origin: (origin, callback) => {
    if (!origin || allowedOrigins.includes(normalizeOrigin(origin))) {
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

const serveUploads = express.static(uploadsPath, {
  dotfiles: 'deny',
  index: false,
});

const registerRoutes = (prefix = '') => {
  app.use(`${prefix}/uploads`, serveUploads);
  app.use(`${prefix}/api/auth`, authRoutes);
  app.use(`${prefix}/api/news`, newsRoutes);
  app.use(`${prefix}/api/banners`, bannerRoutes);
  app.use(`${prefix}/api/expertise`, expertiseRoutes);
  app.use(`${prefix}/api/categories`, categoryRoutes);
  app.use(`${prefix}/api/campaigns`, campaignRoutes);
  app.use(`${prefix}/api/clients`, clientRoutes);
  app.use(`${prefix}/api/careers`, careerRoutes);
  app.use(`${prefix}/api/products`, productRoutes);
  app.use(`${prefix}/api/works`, workRoutes);
  app.use(`${prefix}/api/contact`, contactRoutes);
  app.use(`${prefix}/api/profile`, profileRoutes);
};

registerRoutes();

if (appBasePath && appBasePath !== '/') {
  registerRoutes(appBasePath);
}

app.get('/', (req, res) => {
  res.send('PT Havor Smarta Technology API is running...');
});

if (appBasePath && appBasePath !== '/') {
  app.get(appBasePath, (req, res) => {
    res.send('PT Havor Smarta Technology API is running...');
  });
}

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
