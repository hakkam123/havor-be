const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const fs = require('fs');
const path = require('path');
const slugify = require('slugify');

// @desc    Get all news
// @route   GET /api/news
// @access  Public
const getAllNews = async (req, res) => {
  try {
    const news = await sequelize.query(
      'SELECT * FROM news ORDER BY createdAt DESC',
      { type: QueryTypes.SELECT }
    );
    res.json(news);
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// @desc    Get news by slug
// @route   GET /api/news/:slug
// @access  Public
const getNewsBySlug = async (req, res) => {
  try {
    const news = await sequelize.query(
      'SELECT * FROM news WHERE slug = ?',
      { replacements: [req.params.slug], type: QueryTypes.SELECT }
    );
    if (news.length > 0) {
      res.json(news[0]);
    } else {
      res.status(404).json({ message: 'News not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// @desc    Create news
// @route   POST /api/news
// @access  Private/Admin
const createNews = async (req, res) => {
  const { title, content, category, is_published } = req.body;
  const slug = slugify(title, { lower: true, strict: true });
  const image_url = req.file ? `/uploads/news/${req.file.filename}` : null;
  const published = is_published === 'true' || is_published === true ? 1 : 0;

  try {
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
    res.status(500).json({ message: error.message });
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
        slug = slugify(title, { lower: true, strict: true });
      }

      if (req.file) {
        if (news.image_url) {
          const oldPath = path.join(__dirname, '../../', news.image_url);
          if (fs.existsSync(oldPath)) fs.unlinkSync(oldPath);
        }
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
      res.status(404).json({ message: 'News not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
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
      if (news.image_url) {
        const imagePath = path.join(__dirname, '../../', news.image_url);
        if (fs.existsSync(imagePath)) fs.unlinkSync(imagePath);
      }
      await sequelize.query(
        'DELETE FROM news WHERE id = ?',
        { replacements: [req.params.id], type: QueryTypes.DELETE }
      );
      res.json({ message: 'News removed' });
    } else {
      res.status(404).json({ message: 'News not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

module.exports = {
  getAllNews,
  getNewsBySlug,
  createNews,
  updateNews,
  deleteNews
};
