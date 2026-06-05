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
    `SELECT id FROM campaigns WHERE ${condition} LIMIT 1`,
    { replacements, type: QueryTypes.SELECT }
  );

  return existing.length === 0;
};

const getAllCampaigns = async (req, res) => {
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
      ? await sequelize.query(`SELECT COUNT(*) AS total FROM campaigns ${whereClause}`, { replacements, type: QueryTypes.SELECT })
      : [{ total: 0 }];
    const dataReplacements = [...replacements];
    const paginationSql = pagination ? ' LIMIT ? OFFSET ?' : '';
    if (pagination) dataReplacements.push(pagination.limit, pagination.offset);
    const campaigns = await sequelize.query(
      `SELECT * FROM campaigns ${whereClause} ORDER BY createdAt DESC${paginationSql}`,
      { replacements: dataReplacements, type: QueryTypes.SELECT }
    );
    sendListResponse(res, campaigns, pagination, countResult[0]?.total);
  } catch (error) {
    serverError(res, error);
  }
};

const getCampaignBySlug = async (req, res) => {
  try {
    const whereClause = req.admin ? 'slug = ?' : 'slug = ? AND is_published = 1';
    const campaigns = await sequelize.query(
      `SELECT * FROM campaigns WHERE ${whereClause} LIMIT 1`,
      { replacements: [req.params.slug], type: QueryTypes.SELECT }
    );

    if (!campaigns.length) {
      return notFound(res, 'Campaign not found');
    }

    res.json(campaigns[0]);
  } catch (error) {
    serverError(res, error);
  }
};

const createCampaign = async (req, res) => {
  const { title, content, category, is_published } = req.body;
  const slug = makeSlug(title);
  const image_url = req.file ? `/uploads/campaigns/${req.file.filename}` : null;
  const published = is_published === 'true' || is_published === true ? 1 : 0;

  try {
    const isUnique = await ensureUniqueSlug(slug);
    if (!isUnique) {
      cleanupUploadedFile(req);
      return conflictError(res, 'title', 'A campaign with this title already exists');
    }

    const [result] = await sequelize.query(
      `INSERT INTO campaigns (title, slug, content, category, is_published, image_url, createdAt, updatedAt)
       VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())`,
      {
        replacements: [title, slug, content, category, published, image_url],
        type: QueryTypes.INSERT,
      }
    );

    res.status(201).json({ id: result, title, slug, content, category, is_published: !!published, image_url });
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

const updateCampaign = async (req, res) => {
  try {
    const campaignResult = await sequelize.query(
      'SELECT * FROM campaigns WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );

    if (!campaignResult.length) {
      cleanupUploadedFile(req);
      return notFound(res, 'Campaign not found');
    }

    const campaign = campaignResult[0];
    const { title, content, category, is_published } = req.body;
    let { image_url, slug } = campaign;

    if (title) {
      slug = makeSlug(title);
      const isUnique = await ensureUniqueSlug(slug, req.params.id);
      if (!isUnique) {
        cleanupUploadedFile(req);
        return conflictError(res, 'title', 'A campaign with this title already exists');
      }
    }

    if (req.file) {
      removeFile(campaign.image_url);
      image_url = `/uploads/campaigns/${req.file.filename}`;
    }

    let published = campaign.is_published;
    if (is_published !== undefined) {
      published = is_published === 'true' || is_published === true ? 1 : 0;
    }

    await sequelize.query(
      `UPDATE campaigns SET
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
          title || campaign.title,
          slug,
          content || campaign.content,
          category || campaign.category,
          published,
          image_url,
          req.params.id,
        ],
        type: QueryTypes.UPDATE,
      }
    );

    res.json({
      id: Number(req.params.id),
      title: title || campaign.title,
      slug,
      content: content || campaign.content,
      category: category || campaign.category,
      is_published: !!published,
      image_url,
    });
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

const deleteCampaign = async (req, res) => {
  try {
    const campaignResult = await sequelize.query(
      'SELECT * FROM campaigns WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );

    if (!campaignResult.length) {
      return notFound(res, 'Campaign not found');
    }

    const campaign = campaignResult[0];
    removeFile(campaign.image_url);
    await sequelize.query(
      'DELETE FROM campaigns WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.DELETE }
    );

    res.json({ success: true, message: 'Campaign removed' });
  } catch (error) {
    serverError(res, error);
  }
};

module.exports = {
  getAllCampaigns,
  getCampaignBySlug,
  createCampaign,
  updateCampaign,
  deleteCampaign,
};
