const ALLOWED_LIMITS = [1, 5, 10, 25, 100];

const hasPaginationQuery = (query = {}) => query.page !== undefined || query.limit !== undefined;

const toPositiveInteger = (value, fallback) => {
  const numberValue = Number(value);
  if (!Number.isInteger(numberValue) || numberValue < 1) return fallback;
  return numberValue;
};

const getPagination = (query = {}) => {
  if (!hasPaginationQuery(query)) return null;

  const page = toPositiveInteger(query.page, 1);
  const requestedLimit = toPositiveInteger(query.limit, 10);
  const limit = ALLOWED_LIMITS.includes(requestedLimit) ? requestedLimit : 10;
  const offset = (page - 1) * limit;

  return { page, limit, offset };
};

const buildPaginationMeta = ({ total, page, limit }) => {
  const pageCount = Math.max(1, Math.ceil(total / limit));

  return {
    total,
    page,
    limit,
    pageCount,
    hasNextPage: page < pageCount,
    hasPreviousPage: page > 1,
  };
};

const sendListResponse = (res, data, pagination, total) => {
  if (!pagination) return res.json(data);

  return res.json({
    data,
    meta: buildPaginationMeta({
      total: Number(total) || 0,
      page: pagination.page,
      limit: pagination.limit,
    }),
  });
};

module.exports = {
  ALLOWED_LIMITS,
  getPagination,
  sendListResponse,
};
