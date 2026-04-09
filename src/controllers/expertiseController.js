const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const fs = require('fs');
const path = require('path');

// @desc    Get all expertises
// @route   GET /api/expertise
// @access  Public
const getAllExpertises = async (req, res) => {
  try {
    const data = await sequelize.query(
      'SELECT * FROM expertises ORDER BY createdAt DESC',
      { type: QueryTypes.SELECT }
    );
    res.json(data);
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// @desc    Create expertise
// @route   POST /api/expertise
// @access  Private/Admin
const createExpertise = async (req, res) => {
  const { name, description } = req.body;
  const icon_url = req.file ? `/uploads/expertise/${req.file.filename}` : null;
  try {
    const [result] = await sequelize.query(
      `INSERT INTO expertises (name, description, icon_url, createdAt, updatedAt) 
       VALUES (?, ?, ?, NOW(), NOW())`,
      {
        replacements: [name, description, icon_url],
        type: QueryTypes.INSERT
      }
    );
    res.status(201).json({ id: result, name, description, icon_url });
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// @desc    Update expertise
// @route   PUT /api/expertise/:id
// @access  Private/Admin
const updateExpertise = async (req, res) => {
  try {
    const results = await sequelize.query(
      'SELECT * FROM expertises WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );
    
    if (results.length > 0) {
      const data = results[0];
      const { name, description } = req.body;
      let icon_url = data.icon_url;

      if (req.file) {
        if (data.icon_url) {
          const oldPath = path.join(__dirname, '../../', data.icon_url);
          if (fs.existsSync(oldPath)) fs.unlinkSync(oldPath);
        }
        icon_url = `/uploads/expertise/${req.file.filename}`;
      }

      await sequelize.query(
        `UPDATE expertises SET 
          name = ?, 
          description = ?, 
          icon_url = ?, 
          updatedAt = NOW() 
         WHERE id = ?`,
        {
          replacements: [
            name || data.name,
            description || data.description,
            icon_url,
            req.params.id
          ],
          type: QueryTypes.UPDATE
        }
      );
      res.json({ id: req.params.id, name, description, icon_url });
    } else {
      res.status(404).json({ message: 'Expertise not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// @desc    Delete expertise
// @route   DELETE /api/expertise/:id
// @access  Private/Admin
const deleteExpertise = async (req, res) => {
  try {
    const results = await sequelize.query(
      'SELECT * FROM expertises WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );

    if (results.length > 0) {
      const data = results[0];
      if (data.icon_url) {
        const filePath = path.join(__dirname, '../../', data.icon_url);
        if (fs.existsSync(filePath)) fs.unlinkSync(filePath);
      }
      await sequelize.query(
        'DELETE FROM expertises WHERE id = ?',
        { replacements: [req.params.id], type: QueryTypes.DELETE }
      );
      res.json({ message: 'Expertise removed' });
    } else {
      res.status(404).json({ message: 'Expertise not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

module.exports = { getAllExpertises, createExpertise, updateExpertise, deleteExpertise };
