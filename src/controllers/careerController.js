const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const fs = require('fs');
const path = require('path');

// @desc    Get all careers
// @route   GET /api/careers
// @access  Public
const getAllCareers = async (req, res) => {
    try {
        const careers = await sequelize.query(
            'SELECT * FROM careers ORDER BY job_title ASC',
            { type: QueryTypes.SELECT }
        );
        res.json(careers);
    } catch (error) {
        res.status(500).json({ error: 'Internal server error' });
    }
};

// @desc    Create career
// @route   POST /api/careers
// @access  Private/Admin
const createCareer = async (req, res) => {
    const { job_title, job_description } = req.body;
    const thumbnail = req.file ? `/uploads/careers/${req.file.filename}` : null;
    
    try {
        const result = await sequelize.query(
            'INSERT INTO careers (thumbnail, job_title, job_description, createdAt, updatedAt) VALUES (?, ?, ?, NOW(), NOW())',
            {
                replacements: [thumbnail, job_title, job_description],
                type: QueryTypes.INSERT
            }
        );
        res.status(201).json({ id: result[0], thumbnail, job_title, job_description });
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
};

// @desc    Update career
// @route   PUT /api/careers/:id
// @access  Private/Admin
const updateCareer = async (req, res) => {
    const { job_title, job_description } = req.body;
    const { id } = req.params;
    let thumbnail = null;

    try {
        const existingCareer = await sequelize.query(
            'SELECT * FROM careers WHERE id = ?',
            { replacements: [id], type: QueryTypes.SELECT }
        );

        if (existingCareer.length === 0) {
            return res.status(404).json({ message: 'Career not found' });
        }

        if (req.file) {
            thumbnail = `/uploads/careers/${req.file.filename}`;
        } else {
            thumbnail = existingCareer[0].thumbnail;
        }

        await sequelize.query(
            'UPDATE careers SET thumbnail = ?, job_title = ?, job_description = ?, updatedAt = NOW() WHERE id = ?',
            {
                replacements: [thumbnail, job_title, job_description, id],
                type: QueryTypes.UPDATE
            }
        );
        res.json({ message: 'Career updated successfully' });
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
};

// @desc    Delete career
// @route   DELETE /api/careers/:id
// @access  Private/Admin
const deleteCareer = async (req, res) => {
    try {
        const careerResult = await sequelize.query(
            'SELECT * FROM careers WHERE id = ?',
            { replacements: [req.params.id], type: QueryTypes.SELECT }
        );

        if (careerResult.length > 0) {
            const career = careerResult[0];
            if (career.thumbnail) {
                const thumbnailPath = path.join(__dirname, '..', '..', career.thumbnail.replace('/uploads/', 'uploads/'));
                if (fs.existsSync(thumbnailPath)) {
                    fs.unlinkSync(thumbnailPath);
                }
            }
        }

        await sequelize.query(
            'DELETE FROM careers WHERE id = ?',
            { replacements: [req.params.id], type: QueryTypes.DELETE }
        );
        res.json({ message: 'Career removed' });
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
};

module.exports = {
    getAllCareers,
    createCareer,
    updateCareer,
    deleteCareer
};