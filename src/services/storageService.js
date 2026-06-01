const path = require('path');
const crypto = require('crypto');
const {
  DeleteObjectCommand,
  GetObjectCommand,
  PutObjectCommand,
  S3Client,
} = require('@aws-sdk/client-s3');
const { getSignedUrl } = require('@aws-sdk/s3-request-presigner');

const DEFAULT_SIGNED_URL_EXPIRES_IN = 15 * 60;

class StorageConfigurationError extends Error {
  constructor(message = 'Object storage is not configured') {
    super(message);
    this.name = 'StorageConfigurationError';
    this.statusCode = 503;
  }
}

const truthy = (value) => ['1', 'true', 'yes'].includes(String(value || '').toLowerCase());

const trimTrailingSlash = (value = '') => value.replace(/\/+$/, '');

const getStorageConfig = () => {
  const endpoint = process.env.OBJECT_STORAGE_ENDPOINT || '';
  const region = process.env.OBJECT_STORAGE_REGION || process.env.AWS_REGION || 'auto';
  const bucket = process.env.OBJECT_STORAGE_BUCKET || process.env.AWS_S3_BUCKET || '';
  const accessKeyId = process.env.OBJECT_STORAGE_ACCESS_KEY_ID || process.env.AWS_ACCESS_KEY_ID || '';
  const secretAccessKey = process.env.OBJECT_STORAGE_SECRET_ACCESS_KEY || process.env.AWS_SECRET_ACCESS_KEY || '';
  const publicBaseUrl = trimTrailingSlash(process.env.OBJECT_STORAGE_PUBLIC_BASE_URL || '');
  const forcePathStyle = truthy(process.env.OBJECT_STORAGE_FORCE_PATH_STYLE);

  return {
    endpoint,
    region,
    bucket,
    accessKeyId,
    secretAccessKey,
    publicBaseUrl,
    forcePathStyle,
  };
};

const getMissingConfigKeys = () => {
  const config = getStorageConfig();
  const missingKeys = [];

  if (!config.region) missingKeys.push('OBJECT_STORAGE_REGION or AWS_REGION');
  if (!config.bucket) missingKeys.push('OBJECT_STORAGE_BUCKET or AWS_S3_BUCKET');
  if (!config.accessKeyId) missingKeys.push('OBJECT_STORAGE_ACCESS_KEY_ID or AWS_ACCESS_KEY_ID');
  if (!config.secretAccessKey) missingKeys.push('OBJECT_STORAGE_SECRET_ACCESS_KEY or AWS_SECRET_ACCESS_KEY');

  return missingKeys;
};

const isStorageConfigured = () => getMissingConfigKeys().length === 0;

const assertStorageConfigured = () => {
  const missingKeys = getMissingConfigKeys();

  if (missingKeys.length) {
    throw new StorageConfigurationError(`Object storage is not configured. Missing: ${missingKeys.join(', ')}`);
  }
};

let s3Client;
let s3ClientSignature;

const getClient = () => {
  assertStorageConfigured();

  const config = getStorageConfig();
  const signature = [
    config.endpoint,
    config.region,
    config.accessKeyId,
    config.forcePathStyle,
  ].join('|');

  if (s3Client && s3ClientSignature === signature) return s3Client;

  s3Client = new S3Client({
    endpoint: config.endpoint || undefined,
    region: config.region,
    forcePathStyle: config.forcePathStyle,
    credentials: {
      accessKeyId: config.accessKeyId,
      secretAccessKey: config.secretAccessKey,
    },
  });
  s3ClientSignature = signature;

  return s3Client;
};

const sanitizeFileName = (fileName = 'file.pdf') => {
  const extension = path.extname(fileName).toLowerCase() || '.pdf';
  const baseName = path
    .basename(fileName, extension)
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .slice(0, 80);

  return `${baseName || 'file'}${extension}`;
};

const buildStorageKey = ({ folder = 'careers', originalName }) => {
  const now = new Date();
  const year = now.getUTCFullYear();
  const month = String(now.getUTCMonth() + 1).padStart(2, '0');
  const randomId = crypto.randomBytes(10).toString('hex');

  return [folder, String(year), month, `${randomId}-${sanitizeFileName(originalName)}`].join('/');
};

const buildPublicUrl = (key) => {
  const { publicBaseUrl } = getStorageConfig();
  if (!publicBaseUrl) return null;
  return `${publicBaseUrl}/${key}`;
};

const uploadBuffer = async ({
  buffer,
  originalName,
  mimeType = 'application/octet-stream',
  size,
  folder = 'careers',
  metadata = {},
}) => {
  assertStorageConfigured();

  if (!Buffer.isBuffer(buffer) || buffer.length === 0) {
    throw new Error('Storage upload requires a non-empty file buffer');
  }

  const config = getStorageConfig();
  const key = buildStorageKey({ folder, originalName });

  const command = new PutObjectCommand({
    Bucket: config.bucket,
    Key: key,
    Body: buffer,
    ContentLength: size || buffer.length,
    ContentType: mimeType,
    Metadata: Object.entries(metadata).reduce((safeMetadata, [metadataKey, metadataValue]) => {
      if (metadataValue === undefined || metadataValue === null) return safeMetadata;
      safeMetadata[metadataKey] = String(metadataValue).slice(0, 2000);
      return safeMetadata;
    }, {}),
  });

  const result = await getClient().send(command);

  return {
    bucket: config.bucket,
    key,
    originalName,
    mimeType,
    size: size || buffer.length,
    etag: result.ETag || null,
    publicUrl: buildPublicUrl(key),
    signedUrlStrategy: 'Use getSignedObjectUrl(key) for private admin access.',
    createdAt: new Date().toISOString(),
  };
};

const getSignedObjectUrl = async (key, expiresIn = DEFAULT_SIGNED_URL_EXPIRES_IN) => {
  assertStorageConfigured();

  if (!key) {
    throw new Error('Storage key is required to create a signed URL');
  }

  const { bucket } = getStorageConfig();
  const command = new GetObjectCommand({
    Bucket: bucket,
    Key: key,
  });

  return getSignedUrl(getClient(), command, { expiresIn });
};

const deleteObject = async (key) => {
  assertStorageConfigured();

  if (!key) return;

  const { bucket } = getStorageConfig();
  const command = new DeleteObjectCommand({
    Bucket: bucket,
    Key: key,
  });

  await getClient().send(command);
};

module.exports = {
  StorageConfigurationError,
  buildStorageKey,
  deleteObject,
  getMissingConfigKeys,
  getSignedObjectUrl,
  getStorageConfig,
  isStorageConfigured,
  uploadBuffer,
};
