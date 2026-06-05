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

const validationError = (res, errors, message = 'Please review the required fields and try again.', statusCode = 422) => {
  return res.status(statusCode).json({
    success: false,
    code: statusCode === 409 ? 'CONFLICT' : 'VALIDATION_ERROR',
    message,
    errors,
  });
};

const notFound = (res, message = 'Resource not found') => {
  return res.status(404).json({
    success: false,
    code: 'NOT_FOUND',
    message,
  });
};

const serverError = (res, error, message = 'Internal server error') => {
  if (process.env.NODE_ENV !== 'production') {
    console.error(error);
  }

  return res.status(500).json({
    success: false,
    code: 'INTERNAL_SERVER_ERROR',
    message,
  });
};

const conflictError = (res, field, message) => {
  return validationError(res, { [field]: message }, message, 409);
};

const relationError = (res, field, message) => {
  return res.status(409).json({
    success: false,
    code: 'RESOURCE_IN_USE',
    message,
    errors: {
      [field]: message,
    },
  });
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
  relationError,
  serverError,
  toValidationErrors,
  validationError,
};
