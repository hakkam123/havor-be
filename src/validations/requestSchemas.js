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
    subject: Joi.string().trim().min(2).max(255).required(),
    message: Joi.string().trim().min(5).max(5000).required(),
  }),
};

const normalizeOptionalUrl = (value, helpers) => {
  if (!value) return value;

  const normalizedValue = /^https?:\/\//i.test(value) ? value : `https://${value}`;

  try {
    const url = new URL(normalizedValue);
    if (!['http:', 'https:'].includes(url.protocol) || !url.hostname.includes('.')) {
      return helpers.error('url.invalid');
    }

    return normalizedValue;
  } catch (error) {
    return helpers.error('url.invalid');
  }
};

const validatePhoneNumber = (value, helpers) => {
  if (!/^[+\d\s()-]+$/.test(value)) {
    return helpers.error('phone.invalidCharacters');
  }

  const digitCount = value.replace(/\D/g, '').length;
  if (digitCount < 10) return helpers.error('phone.minDigits');
  if (digitCount > 15) return helpers.error('phone.maxDigits');

  return value;
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
  application: Joi.object({
    fullName: Joi.string().trim().min(2).max(255).required().messages({
      'any.required': 'Nama panjang wajib diisi.',
      'string.empty': 'Nama panjang wajib diisi.',
      'string.min': 'Nama panjang minimal 2 karakter.',
    }),
    email: Joi.string().email().max(255).required().messages({
      'any.required': 'Email wajib diisi.',
      'string.empty': 'Email wajib diisi.',
      'string.email': 'Masukkan email yang valid, contoh nama@gmail.com.',
    }),
    phone: Joi.string().trim().required().custom(validatePhoneNumber).messages({
      'any.required': 'Nomor telepon wajib diisi.',
      'string.empty': 'Nomor telepon wajib diisi.',
      'phone.invalidCharacters': 'Nomor telepon hanya boleh berisi angka, spasi, tanda +, -, atau ().',
      'phone.minDigits': 'Nomor telepon minimal 10 digit.',
      'phone.maxDigits': 'Nomor telepon maksimal 15 digit.',
    }),
    address: Joi.string().trim().max(5000).allow('', null),
    position: Joi.string().trim().min(2).max(255).required().messages({
      'any.required': 'Posisi yang dilamar wajib dipilih.',
      'string.empty': 'Posisi yang dilamar wajib dipilih.',
    }),
    latestEducation: Joi.string().trim().max(255).allow('', null),
    experienceSummary: Joi.string().trim().max(255).allow('', null),
    portfolioUrl: Joi.string().trim().max(2048).allow('', null).custom(normalizeOptionalUrl).messages({
      'url.invalid': 'Masukkan URL yang valid, contoh https://www.google.com.',
    }),
    message: Joi.string().trim().min(5).max(10000).messages({
      'string.empty': 'Pesan atau cover letter singkat wajib diisi.',
      'string.min': 'Pesan atau cover letter minimal 5 karakter.',
    }),
    coverLetter: Joi.string().trim().min(5).max(10000).messages({
      'string.empty': 'Pesan atau cover letter singkat wajib diisi.',
      'string.min': 'Pesan atau cover letter minimal 5 karakter.',
    }),
  }).or('message', 'coverLetter').messages({
    'object.missing': 'Pesan atau cover letter singkat wajib diisi.',
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

const profile = {
  update: Joi.object({
    company_name: Joi.string().trim().min(2).max(255).required(),
    tagline: Joi.string().trim().max(255).allow('', null),
    short_description: Joi.string().trim().max(5000).allow('', null),
    long_description: Joi.string().trim().max(100000).allow('', null),
    email: Joi.string().email().max(255).allow('', null),
    phone: Joi.string().trim().max(100).allow('', null),
    website: Joi.string().uri({ scheme: ['http', 'https'] }).max(2048).allow('', null),
    address: Joi.string().trim().max(5000).allow('', null),
    linkedin_url: Joi.string().uri({ scheme: ['http', 'https'] }).max(2048).allow('', null),
    instagram_url: Joi.string().uri({ scheme: ['http', 'https'] }).max(2048).allow('', null),
    logo_url: Joi.string().allow('', null),
    seo_title: Joi.string().trim().max(255).allow('', null),
    seo_description: Joi.string().trim().max(5000).allow('', null),
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
  profile,
  work,
};
