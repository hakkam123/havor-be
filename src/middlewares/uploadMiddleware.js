const multer = require('multer');
const path = require('path');
const fs = require('fs');

const uploadRoot = path.join(__dirname, '../../uploads');
const uploadFolders = {
  news: 'news',
  banners: 'banners',
  careers: 'careers',
  applications: 'career-applications',
  clients: 'clients',
  expertise: 'expertise',
  profile: 'profile',
  products: 'products',
  works: 'works',
};

const getUploadFolder = (baseUrl) => {
  if (baseUrl.includes('careers')) return uploadFolders.careers;

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
  const documentFieldNames = new Set(['cv', 'resume', 'portfolio']);
  const documentMimeTypes = new Set([
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  ]);
  const documentExtensions = new Set(['.pdf', '.doc', '.docx']);
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

  if (documentFieldNames.has(file.fieldname)) {
    if (documentExtensions.has(extname) && documentMimeTypes.has(file.mimetype)) {
      return cb(null, true);
    }

    const error = new Error('Resume or portfolio file must be PDF, DOC, or DOCX.');
    error.statusCode = 422;
    return cb(error);
  }

  if (allowedExtensions.has(extname) && allowedMimeTypes.has(file.mimetype)) {
    return cb(null, true);
  }

  const error = new Error('Only images (jpg, png, webp) and videos (mp4, mov, avi) are allowed');
  error.statusCode = 422;
  cb(error);
};

const upload = multer({
  storage,
  fileFilter,
  limits: { fileSize: 50 * 1024 * 1024 },
});

const resumeUpload = multer({
  storage: multer.memoryStorage(),
  fileFilter: (req, file, cb) => {
    const extension = path.extname(file.originalname).toLowerCase();
    const isPdf = extension === '.pdf' && file.mimetype === 'application/pdf';

    if (isPdf) return cb(null, true);

    const error = new Error('Resume must be a PDF file.');
    error.statusCode = 422;
    return cb(error);
  },
  limits: { fileSize: 2 * 1024 * 1024 },
});

const normalizeResumeFile = (req, res, next) => {
  const resume = req.files?.resume?.[0] || req.files?.cv?.[0] || null;
  req.file = resume;
  next();
};

module.exports = { normalizeResumeFile, resumeUpload, upload };
