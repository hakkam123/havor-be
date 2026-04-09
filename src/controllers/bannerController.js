const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const fs = require('fs');
const path = require('path');

// @desc    Get all banners
// @route   GET /api/banners
// @access  Public
const getAllBanners = async (req, res) => {
  try {
    const banners = await sequelize.query(
      'SELECT * FROM hero_banners',
      { type: QueryTypes.SELECT }
    );
    res.json(banners);
  } catch (error) {
    res.status(500).json({ message: error.message });
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
      res.status(404).json({ message: 'Banner not found for this page' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// @desc    Create or Update banner
// @route   POST /api/banners
// @access  Private/Admin
const upsertBanner = async (req, res) => {
  const { page_name, title, subtitle, media_type } = req.body;
  
  if (!req.file && !req.body.media_url) {
    return res.status(400).json({ message: 'Media file or URL is required' });
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
        const oldPath = path.join(__dirname, '../../', banner.media_url);
        if (fs.existsSync(oldPath)) fs.unlinkSync(oldPath);
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
    res.status(500).json({ message: error.message });
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
        const mediaPath = path.join(__dirname, '../../', banner.media_url);
        if (fs.existsSync(mediaPath)) fs.unlinkSync(mediaPath);
      }
      await sequelize.query(
        'DELETE FROM hero_banners WHERE id = ?',
        { replacements: [req.params.id], type: QueryTypes.DELETE }
      );
      res.json({ message: 'Banner removed' });
    } else {
      res.status(404).json({ message: 'Banner not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

module.exports = {
  getAllBanners,
  getBannerByPage,
  upsertBanner,
  deleteBanner
};
