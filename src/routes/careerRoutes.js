const express = require('express');
const router = express.Router();
const {
    getAllCareers,
    submitCareerApplication,
    createCareer,
    updateCareer,
    deleteCareer
} = require('../controllers/careerController');
const { protect } = require('../middlewares/authMiddleware');
const { normalizeResumeFile, resumeUpload, upload } = require('../middlewares/uploadMiddleware');
const { createRateLimiter, validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getAllCareers);

const applicationRateLimiter = createRateLimiter({
  windowMs: 15 * 60 * 1000,
  max: 5,
  message: 'Too many career applications. Please try again later.',
});

const handleApplicationUploadError = (res, uploadError) => {
  if (uploadError.code === 'LIMIT_FILE_SIZE') {
    return res.status(422).json({
      success: false,
      message: 'Ukuran CV maksimal 2 MB.',
    });
  }

  return res.status(uploadError.statusCode || 400).json({
    success: false,
    message: uploadError.message || 'Upload CV gagal.',
  });
};

const routePostCareer = (req, res, next) => {
  if (req.headers.authorization?.startsWith('Bearer')) {
    return protect(req, res, () => {
      upload.single('thumbnail')(req, res, (uploadError) => {
        if (uploadError) return next(uploadError);
        return validate(schemas.career.create)(req, res, () => createCareer(req, res));
      });
    });
  }

  return applicationRateLimiter(req, res, () => {
    resumeUpload.fields([
      { name: 'resume', maxCount: 1 },
      { name: 'cv', maxCount: 1 },
    ])(req, res, (uploadError) => {
      if (uploadError) return handleApplicationUploadError(res, uploadError);
      normalizeResumeFile(req, res, () => {
        validate(schemas.career.application)(req, res, () => submitCareerApplication(req, res));
      });
    });
  });
};

router.post('/', routePostCareer);
router.put('/:id', protect, validate(schemas.idParam, 'params'), upload.single('thumbnail'), validate(schemas.career.update), updateCareer);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteCareer);

module.exports = router;
