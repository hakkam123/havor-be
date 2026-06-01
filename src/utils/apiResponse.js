const fs = require('fs');
const path = require('path');

const uploadRoot = path.join(__dirname, '../../uploads');

const normalizeFieldName = (pathValue) => {
  if (Array.isArray(pathValue)) return pathValue.join('.');
  return String(pathValue || 'field');
};

const toValidationErrors = (details = []) => {
  return details.reduce((errors, detail) => {
    const field = normalizeFieldName(detail.path);
    errors[field] = detail.message.replace(/"/g, '');
    return errors;
  }, {});
};

const validationError = (res, errors, message = 'Mohon periksa kembali data yang wajib diisi.', statusCode = 422) => {
  return res.status(statusCode).json({
    success: false,
    message,
    errors,
  });
};

const notFound = (res, message = 'Resource not found') => {
  return res.status(404).json({
    success: false,
    message,
  });
};

const serverError = (res, error, message = 'Internal server error') => {
  if (process.env.NODE_ENV !== 'production') {
    console.error(error);
  }

  return res.status(500).json({
    success: false,
    message,
  });
};

const conflictError = (res, field, message) => {
  return validationError(res, { [field]: message }, 'Validation failed', 409);
};

const isDuplicateEntry = (error) => {
  return error?.original?.code === 'ER_DUP_ENTRY' || error?.parent?.code === 'ER_DUP_ENTRY';
};

const toAbsoluteUploadPath = (uploadPath) => {
  if (!uploadPath || !uploadPath.startsWith('/uploads/')) return null;
  return path.join(uploadRoot, uploadPath.replace('/uploads/', ''));
};

const removeFile = (uploadPath) => {
  const absolutePath = toAbsoluteUploadPath(uploadPath);
  if (absolutePath && fs.existsSync(absolutePath)) {
    fs.unlinkSync(absolutePath);
  }
};

const cleanupUploadedFile = (req) => {
  if (req?.file?.path && fs.existsSync(req.file.path)) {
    fs.unlinkSync(req.file.path);
  }
};

module.exports = {
  cleanupUploadedFile,
  conflictError,
  isDuplicateEntry,
  notFound,
  removeFile,
  serverError,
  toValidationErrors,
  validationError,
};
