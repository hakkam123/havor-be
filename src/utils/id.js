const crypto = require('crypto');

const createId = () => crypto.randomUUID();

module.exports = { createId };
