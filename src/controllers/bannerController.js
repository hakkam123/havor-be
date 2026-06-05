const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const {
  cleanupUploadedFile,
  conflictError,
  isDuplicateEntry,
  notFound,
  removeFile,
  serverError,
  validationError,
} = require('../utils/apiResponse');
const { getPagination, sendListResponse } = require('../utils/pagination');

// @desc    Get all banners
// @route   GET /api/banners
// @access  Public
const getAllBanners = async (req, res) => {
  try {
    const pagination = getPagination(req.query);
    const replacements = [];
    const where = [];
    const search = String(req.query.search || '').trim();

    if (search) {
      const keyword = `%${search}%`;
      where.push('(page_name LIKE ? OR title LIKE ? OR subtitle LIKE ? OR media_type LIKE ?)');
      replacements.push(keyword, keyword, keyword, keyword);
    }

    const whereClause = where.length ? `WHERE ${where.join(' AND ')}` : '';
    const countResult = pagination
      ? await sequelize.query(`SELECT COUNT(*) AS total FROM hero_banners ${whereClause}`, { replacements, type: QueryTypes.SELECT })
      : [{ total: 0 }];
    const dataReplacements = [...replacements];
    const paginationSql = pagination ? ' LIMIT ? OFFSET ?' : '';
    if (pagination) dataReplacements.push(pagination.limit, pagination.offset);
    const banners = await sequelize.query(
      `SELECT * FROM hero_banners ${whereClause} ORDER BY page_name ASC${paginationSql}`,
      { replacements: dataReplacements, type: QueryTypes.SELECT }
    );
    sendListResponse(res, banners, pagination, countResult[0]?.total);
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Get banner by page name
// @route   GET /api/banners/:page
// @access  Public
const getBannerByPage = async (req, res) => {
  try {
    const banners = await sequelize.query(
      'SELECT * FROM hero_banners WHERE page_name = ?',
      { replacements: [req.params.page], type: QueryTypes.SELECT }
    );
    if (banners.length > 0) {
      res.json(banners[0]);
    } else {
      notFound(res, 'Banner not found for this page');
    }
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Create or Update banner
// @route   POST /api/banners
// @access  Private/Admin
const upsertBanner = async (req, res) => {
  const { page_name, title, subtitle, media_type } = req.body;
  
  if (!req.file && !req.body.media_url) {
    return validationError(res, { media_url: 'Media file or URL is required' });
  }

  try {
    const banners = await sequelize.query(
      'SELECT * FROM hero_banners WHERE page_name = ?',
      { replacements: [page_name], type: QueryTypes.SELECT }
    );

    const media_url = req.file ? `/uploads/banners/${req.file.filename}` : req.body.media_url;

    if (banners.length > 0) {
      // Update
      const banner = banners[0];
      if (req.file && banner.media_url && banner.media_url.startsWith('/uploads')) {
        removeFile(banner.media_url);
      }
      
      await sequelize.query(
        `UPDATE hero_banners SET 
          title = ?, 
          subtitle = ?, 
          media_url = ?, 
          media_type = ?, 
          updatedAt = NOW() 
         WHERE page_name = ?`,
        {
          replacements: [
            title || banner.title,
            subtitle || banner.subtitle,
            media_url || banner.media_url,
            media_type || banner.media_type,
            page_name
          ],
          type: QueryTypes.UPDATE
        }
      );
      res.json({ page_name, title, subtitle, media_url, media_type });
    } else {
      // Create
      const type = media_type || (req.file ? (req.file.mimetype.startsWith('video') ? 'video' : 'image') : 'image');
      const [result] = await sequelize.query(
        `INSERT INTO hero_banners (page_name, title, subtitle, media_url, media_type, createdAt, updatedAt) 
         VALUES (?, ?, ?, ?, ?, NOW(), NOW())`,
        {
          replacements: [page_name, title, subtitle, media_url, type],
          type: QueryTypes.INSERT
        }
      );
      res.status(201).json({ id: result, page_name, title, subtitle, media_url, media_type: type });
    }
  } catch (error) {
    cleanupUploadedFile(req);
    if (isDuplicateEntry(error)) {
      return conflictError(res, 'page_name', 'Banner page name already exists');
    }
    serverError(res, error);
  }
};

// @desc    Delete banner
// @route   DELETE /api/banners/:id
// @access  Private/Admin
const deleteBanner = async (req, res) => {
  try {
    const banners = await sequelize.query(
      'SELECT * FROM hero_banners WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );

    if (banners.length > 0) {
      const banner = banners[0];
      if (banner.media_url && banner.media_url.startsWith('/uploads')) {
        removeFile(banner.media_url);
      }
      await sequelize.query(
        'DELETE FROM hero_banners WHERE id = ?',
        { replacements: [req.params.id], type: QueryTypes.DELETE }
      );
      res.json({ message: 'Banner removed' });
    } else {
      notFound(res, 'Banner not found');
    }
  } catch (error) {
    serverError(res, error);
  }
};

module.exports = {
  getAllBanners,
  getBannerByPage,
  upsertBanner,
  deleteBanner
};
