const multer = require('multer');
const path = require('path');
const fs = require('fs');

// Storage configuration
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    let folder = 'uploads/';
    
    // Determine folder based on fieldname or route if needed
    if (req.baseUrl.includes('news')) folder += 'news/';
    else if (req.baseUrl.includes('banners')) folder += 'banners/';
    else if (req.baseUrl.includes('careers')) folder += 'careers/';
    else if (req.baseUrl.includes('clients')) folder += 'clients/';
    else if (req.baseUrl.includes('expertise')) folder += 'expertise/';
    else if (req.baseUrl.includes('products')) folder += 'products/';
    else if (req.baseUrl.includes('works')) folder += 'works/';

    // Ensure directory exists
    if (!fs.existsSync(folder)) {
      fs.mkdirSync(folder, { recursive: true });
    }
    cb(null, folder);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, file.fieldname + '-' + uniqueSuffix + path.extname(file.originalname));
  }
});

// File filter
const fileFilter = (req, file, cb) => {
  const allowedTypes = /jpeg|jpg|png|webp|mp4|mov|avi/;
  const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
  const mimetype = allowedTypes.test(file.mimetype);

  if (extname && mimetype) {
    return cb(null, true);
  } else {
    cb(new Error('Only images (jpg, png, webp) and videos (mp4, mov, avi) are allowed!'));
  }
};

const upload = multer({
  storage: storage,
  fileFilter: fileFilter,
  limits: { fileSize: 50 * 1024 * 1024 } // 50MB limit for videos
});

module.exports = { upload };
