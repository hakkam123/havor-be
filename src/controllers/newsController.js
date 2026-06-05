const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const slugify = require('slugify');
const {
  cleanupUploadedFile,
  conflictError,
  notFound,
  removeFile,
  serverError,
} = require('../utils/apiResponse');
const { getPagination, sendListResponse } = require('../utils/pagination');

const makeSlug = (title) => slugify(title || '', { lower: true, strict: true });

const ensureUniqueSlug = async (slug, ignoreId = null) => {
  const replacements = ignoreId ? [slug, ignoreId] : [slug];
  const condition = ignoreId ? 'slug = ? AND id != ?' : 'slug = ?';
  const existing = await sequelize.query(
    `SELECT id FROM news WHERE ${condition} LIMIT 1`,
    { replacements, type: QueryTypes.SELECT }
  );

  return existing.length === 0;
};

// @desc    Get all news
// @route   GET /api/news
// @access  Public
const getAllNews = async (req, res) => {
  try {
    const pagination = getPagination(req.query);
    const replacements = [];
    const where = [];
    const search = String(req.query.search || '').trim();
    const status = String(req.query.status || 'all').toLowerCase();
    const category = String(req.query.category || '').trim();

    if (!req.admin) {
      where.push('is_published = 1');
    } else if (status === 'published') {
      where.push('is_published = 1');
    } else if (status === 'draft') {
      where.push('is_published = 0');
    }

    if (search) {
      const keyword = `%${search}%`;
      where.push('(title LIKE ? OR slug LIKE ? OR category LIKE ? OR content LIKE ?)');
      replacements.push(keyword, keyword, keyword, keyword);
    }

    if (category && category !== 'all') {
      where.push('LOWER(category) = LOWER(?)');
      replacements.push(category);
    }

    const whereClause = where.length ? `WHERE ${where.join(' AND ')}` : '';
    const countResult = pagination
      ? await sequelize.query(`SELECT COUNT(*) AS total FROM news ${whereClause}`, { replacements, type: QueryTypes.SELECT })
      : [{ total: 0 }];
    const dataReplacements = [...replacements];
    const paginationSql = pagination ? ' LIMIT ? OFFSET ?' : '';
    if (pagination) dataReplacements.push(pagination.limit, pagination.offset);
    const news = await sequelize.query(
      `SELECT * FROM news ${whereClause} ORDER BY createdAt DESC${paginationSql}`,
      { replacements: dataReplacements, type: QueryTypes.SELECT }
    );
    sendListResponse(res, news, pagination, countResult[0]?.total);
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Get news by slug
// @route   GET /api/news/:slug
// @access  Public
const getNewsBySlug = async (req, res) => {
  try {
    const news = await sequelize.query(
      'SELECT * FROM news WHERE slug = ? AND is_published = 1',
      { replacements: [req.params.slug], type: QueryTypes.SELECT }
    );
    if (news.length > 0) {
      res.json(news[0]);
    } else {
      notFound(res, 'News not found');
    }
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Create news
// @route   POST /api/news
// @access  Private/Admin
const createNews = async (req, res) => {
  const { title, content, category, is_published } = req.body;
  const slug = makeSlug(title);
  const image_url = req.file ? `/uploads/news/${req.file.filename}` : null;
  const published = is_published === 'true' || is_published === true ? 1 : 0;

  try {
    const isUnique = await ensureUniqueSlug(slug);
    if (!isUnique) {
      cleanupUploadedFile(req);
      return conflictError(res, 'title', 'A news item with this title already exists');
    }

    const [result] = await sequelize.query(
      `INSERT INTO news (title, slug, content, category, is_published, image_url, createdAt, updatedAt) 
       VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())`,
      {
        replacements: [title, slug, content, category, published, image_url],
        type: QueryTypes.INSERT
      }
    );
    res.status(201).json({ id: result, title, slug, content, category, is_published: !!published, image_url });
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

// @desc    Update news
// @route   PUT /api/news/:id
// @access  Private/Admin
const updateNews = async (req, res) => {
  try {
    const newsResult = await sequelize.query(
      'SELECT * FROM news WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );
    
    if (newsResult.length > 0) {
      const news = newsResult[0];
      const { title, content, category, is_published } = req.body;
      let { image_url, slug } = news;

      if (title) {
        slug = makeSlug(title);
        const isUnique = await ensureUniqueSlug(slug, req.params.id);
        if (!isUnique) {
          cleanupUploadedFile(req);
          return conflictError(res, 'title', 'A news item with this title already exists');
        }
      }

      if (req.file) {
        removeFile(news.image_url);
        image_url = `/uploads/news/${req.file.filename}`;
      }

      let published = news.is_published;
      if (is_published !== undefined) {
        published = is_published === 'true' || is_published === true ? 1 : 0;
      }

      await sequelize.query(
        `UPDATE news SET 
          title = ?, 
          slug = ?, 
          content = ?, 
          category = ?, 
          is_published = ?, 
          image_url = ?, 
          updatedAt = NOW() 
         WHERE id = ?`,
        {
          replacements: [
            title || news.title,
            slug,
            content || news.content,
            category || news.category,
            published,
            image_url,
            req.params.id
          ],
          type: QueryTypes.UPDATE
        }
      );
      res.json({ id: req.params.id, title, slug, content, category, is_published: !!published, image_url });
    } else {
      cleanupUploadedFile(req);
      notFound(res, 'News not found');
    }
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

// @desc    Delete news
// @route   DELETE /api/news/:id
// @access  Private/Admin
const deleteNews = async (req, res) => {
  try {
    const newsResult = await sequelize.query(
      'SELECT * FROM news WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );

    if (newsResult.length > 0) {
      const news = newsResult[0];
      removeFile(news.image_url);
      await sequelize.query(
        'DELETE FROM news WHERE id = ?',
        { replacements: [req.params.id], type: QueryTypes.DELETE }
      );
      res.json({ message: 'News removed' });
    } else {
      notFound(res, 'News not found');
    }
  } catch (error) {
    serverError(res, error);
  }
};

module.exports = {
  getAllNews,
  getNewsBySlug,
  createNews,
  updateNews,
  deleteNews
};
