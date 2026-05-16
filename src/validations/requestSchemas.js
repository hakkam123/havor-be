const { Joi } = require('../middlewares/securityMiddleware');

const idParam = Joi.object({
  id: Joi.number().integer().positive().required(),
});

const auth = {
  login: Joi.object({
    email: Joi.string().email().max(255).required(),
    password: Joi.string().min(1).max(255).required(),
  }),
  refresh: Joi.object({
    refreshToken: Joi.string().min(20).required(),
  }),
};

const category = {
  create: Joi.object({
    name: Joi.string().trim().min(2).max(255).required(),
  }),
  update: Joi.object({
    name: Joi.string().trim().min(2).max(255).required(),
  }),
};

const contact = {
  submit: Joi.object({
    name: Joi.string().trim().min(2).max(255).required(),
    email: Joi.string().email().max(255).required(),
    subject: Joi.string().trim().max(255).allow('', null),
    message: Joi.string().trim().min(5).max(5000).required(),
  }),
};

const banner = {
  upsert: Joi.object({
    page_name: Joi.string().trim().min(2).max(255).required(),
    title: Joi.string().trim().max(255).allow('', null),
    subtitle: Joi.string().trim().max(255).allow('', null),
    media_url: Joi.string().uri({ scheme: ['http', 'https'] }).max(2048).allow('', null),
    media_type: Joi.string().valid('image', 'video').allow('', null),
  }),
};

const news = {
  create: Joi.object({
    title: Joi.string().trim().min(2).max(255).required(),
    content: Joi.string().trim().min(1).max(100000).required(),
    category: Joi.string().trim().max(255).allow('', null),
    is_published: Joi.boolean().truthy('true').falsy('false').default(false),
  }),
  update: Joi.object({
    title: Joi.string().trim().min(2).max(255),
    content: Joi.string().trim().min(1).max(100000),
    category: Joi.string().trim().max(255).allow('', null),
    is_published: Joi.boolean().truthy('true').falsy('false'),
  }),
};

const product = {
  create: Joi.object({
    name: Joi.string().trim().min(2).max(255).required(),
    description: Joi.string().trim().min(1).max(100000).required(),
    external_link: Joi.string().uri({ scheme: ['http', 'https'] }).max(2048).allow('', null),
    categoryId: Joi.number().integer().positive().empty('').allow(null),
  }),
  update: Joi.object({
    name: Joi.string().trim().min(2).max(255),
    description: Joi.string().trim().min(1).max(100000),
    external_link: Joi.string().uri({ scheme: ['http', 'https'] }).max(2048).allow('', null),
    categoryId: Joi.number().integer().positive().empty('').allow(null),
  }),
};

const work = {
  create: Joi.object({
    title: Joi.string().trim().min(2).max(255).required(),
    description: Joi.string().trim().max(100000).allow('', null),
    client: Joi.string().trim().max(255).allow('', null),
    year: Joi.number().integer().min(1900).max(2100).empty('').allow(null),
    categoryId: Joi.number().integer().positive().empty('').allow(null),
  }),
  update: Joi.object({
    title: Joi.string().trim().min(2).max(255),
    description: Joi.string().trim().max(100000).allow('', null),
    client: Joi.string().trim().max(255).allow('', null),
    year: Joi.number().integer().min(1900).max(2100).empty('').allow(null),
    categoryId: Joi.number().integer().positive().empty('').allow(null),
  }),
};

const career = {
  create: Joi.object({
    job_title: Joi.string().trim().min(2).max(255).required(),
    job_description: Joi.string().trim().min(1).max(100000).required(),
  }),
  update: Joi.object({
    job_title: Joi.string().trim().min(2).max(255).required(),
    job_description: Joi.string().trim().min(1).max(100000).required(),
  }),
};

const client = {
  create: Joi.object({
    name: Joi.string().trim().min(2).max(255).required(),
    description: Joi.string().trim().min(1).max(100000).required(),
  }),
  update: Joi.object({
    name: Joi.string().trim().min(2).max(255).required(),
    description: Joi.string().trim().min(1).max(100000).required(),
  }),
};

const expertise = {
  create: Joi.object({
    name: Joi.string().trim().min(2).max(255).required(),
    description: Joi.string().trim().min(1).max(100000).required(),
  }),
  update: Joi.object({
    name: Joi.string().trim().min(2).max(255),
    description: Joi.string().trim().min(1).max(100000),
  }),
};

module.exports = {
  idParam,
  auth,
  banner,
  career,
  category,
  client,
  contact,
  expertise,
  news,
  product,
  work,
};
