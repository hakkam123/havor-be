const multer = require('multer');
const path = require('path');
const fs = require('fs');

const uploadRoot = path.join(__dirname, '../../uploads');
const uploadFolders = {
  news: 'news',
  banners: 'banners',
  careers: 'careers',
  clients: 'clients',
  expertise: 'expertise',
  products: 'products',
  works: 'works',
};

const getUploadFolder = (baseUrl) => {
  const routeName = Object.keys(uploadFolders).find((name) => baseUrl.includes(name));
  return routeName ? uploadFolders[routeName] : '';
};

// Storage configuration
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const folder = path.join(uploadRoot, getUploadFolder(req.baseUrl));

    if (!fs.existsSync(folder)) {
      fs.mkdirSync(folder, { recursive: true });
    }

    cb(null, folder);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = `${Date.now()}-${Math.round(Math.random() * 1E9)}`;
    const extension = path.extname(file.originalname).toLowerCase();
    cb(null, `${file.fieldname}-${uniqueSuffix}${extension}`);
  }
});

// File filter
const fileFilter = (req, file, cb) => {
  const allowedMimeTypes = new Set([
    'image/jpeg',
    'image/png',
    'image/webp',
    'video/mp4',
    'video/quicktime',
    'video/x-msvideo',
  ]);
  const allowedExtensions = new Set(['.jpeg', '.jpg', '.png', '.webp', '.mp4', '.mov', '.avi']);
  const extname = path.extname(file.originalname).toLowerCase();

  if (allowedExtensions.has(extname) && allowedMimeTypes.has(file.mimetype)) {
    return cb(null, true);
  }

  cb(new Error('Only images (jpg, png, webp) and videos (mp4, mov, avi) are allowed'));
};

const upload = multer({
  storage,
  fileFilter,
  limits: { fileSize: 50 * 1024 * 1024 },
});

module.exports = { upload };
