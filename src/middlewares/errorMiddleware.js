const errorHandler = (err, req, res, next) => {
  let statusCode = res.statusCode === 200 ? 500 : res.statusCode;

  if (err.name === 'MulterError') {
    statusCode = 400;
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
    message,
    stack: isProduction ? undefined : err.stack,
  });
};

module.exports = { errorHandler };
