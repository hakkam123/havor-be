const errorHandler = (err, req, res, next) => {
  let statusCode = err.statusCode || err.status || (res.statusCode === 200 ? 500 : res.statusCode);

  if (err.name === 'MulterError') {
    statusCode = err.code === 'LIMIT_FILE_SIZE' ? 422 : 400;
  }

  if (err.message === 'Not allowed by CORS') {
    statusCode = 403;
  }

  const isProduction = process.env.NODE_ENV === 'production';
  const message = statusCode >= 500 && isProduction
    ? 'Internal server error'
    : err.message;

  res.status(statusCode);
  res.json({
    success: false,
    code: err.code || (statusCode === 403 ? 'FORBIDDEN' : statusCode >= 500 ? 'INTERNAL_SERVER_ERROR' : 'REQUEST_ERROR'),
    message,
    errors: statusCode < 500 ? { file: message } : undefined,
    stack: statusCode >= 500 && !isProduction ? err.stack : undefined,
  });
};

module.exports = { errorHandler };
