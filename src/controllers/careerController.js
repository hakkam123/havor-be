const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const CareerApplication = require('../models/CareerApplication');
const { sendCareerEmails } = require('../services/emailService');
const { getSignedObjectUrl, uploadBuffer } = require('../services/storageService');
const {
    cleanupUploadedFile,
    conflictError,
    notFound,
    removeFile,
    serverError,
    validationError,
} = require('../utils/apiResponse');

const ensureUniqueTitle = async (jobTitle, ignoreId = null) => {
    const replacements = ignoreId ? [jobTitle, ignoreId] : [jobTitle];
    const condition = ignoreId ? 'LOWER(job_title) = LOWER(?) AND id != ?' : 'LOWER(job_title) = LOWER(?)';
    const existing = await sequelize.query(
        `SELECT id FROM careers WHERE ${condition} LIMIT 1`,
        { replacements, type: QueryTypes.SELECT }
    );
    return existing.length === 0;
};

const isStorageProviderError = (error) => {
    const storageErrorCodes = new Set([
        'AccessDenied',
        'InvalidAccessKeyId',
        'NoSuchBucket',
        'SignatureDoesNotMatch',
    ]);

    return storageErrorCodes.has(error.Code || error.code) || error.$metadata?.httpStatusCode >= 400;
};

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
        serverError(res, error);
    }
};

// @desc    Submit public career application
// @route   POST /api/careers
// @access  Public
const submitCareerApplication = async (req, res) => {
    const {
        fullName,
        email,
        phone,
        address,
        position,
        latestEducation,
        experienceSummary,
        portfolioUrl,
        coverLetter,
    } = req.body;
    const message = req.body.message || coverLetter;

    if (!req.file) {
        return res.status(422).json({
            success: false,
            message: 'CV wajib diupload.',
        });
    }

    let storedResume;

    try {
        storedResume = await uploadBuffer({
            buffer: req.file.buffer,
            originalName: req.file.originalname,
            mimeType: req.file.mimetype,
            size: req.file.size,
            folder: 'careers',
            metadata: {
                applicantEmail: email,
                position,
            },
        });

        const application = await CareerApplication.create({
            full_name: fullName,
            email,
            phone,
            address: address || null,
            position,
            latest_education: latestEducation || null,
            experience_summary: experienceSummary || null,
            portfolio_url: portfolioUrl || null,
            message,
            cv_original_name: storedResume.originalName,
            cv_mime_type: storedResume.mimeType,
            cv_size: storedResume.size,
            cv_storage_key: storedResume.key,
            cv_bucket: storedResume.bucket,
            cv_url: storedResume.publicUrl,
            cv_signed_url_strategy: storedResume.signedUrlStrategy,
            status: 'new',
        });

        let resumeReference = storedResume.key;
        try {
            resumeReference = await getSignedObjectUrl(storedResume.key);
        } catch (signedUrlError) {
            if (process.env.NODE_ENV !== 'production') {
                console.error('Failed to create resume signed URL:', signedUrlError.message);
            }
        }

        const emailStatus = await sendCareerEmails({
            application: {
                fullName,
                email,
                phone,
                address,
                position,
                latestEducation,
                experienceSummary,
                portfolioUrl,
                message,
            },
            resumeReference,
        });

        return res.status(201).json({
            success: true,
            message: 'Lamaran berhasil dikirim. Mohon tunggu sebentar, admin akan membalas melalui email.',
            data: {
                id: application.id,
                cvStorageKey: storedResume.key,
                email: {
                    applicant: emailStatus.userEmail.sent,
                    admin: emailStatus.adminEmail.sent,
                },
            },
        });
    } catch (error) {
        if (error.statusCode === 503) {
            return res.status(503).json({
                success: false,
                message: 'Object storage belum dikonfigurasi. Silakan lengkapi environment variable storage terlebih dahulu.',
            });
        }

        if (isStorageProviderError(error)) {
            if (process.env.NODE_ENV !== 'production') {
                console.error('Object storage upload failed:', error.Code || error.code || error.message);
            }

            return res.status(502).json({
                success: false,
                message: 'Upload CV ke storage gagal. Silakan periksa konfigurasi Supabase Storage.',
            });
        }

        serverError(res, error, 'Data belum berhasil dikirim. Silakan coba lagi beberapa saat.');
    }
};

// @desc    Create career
// @route   POST /api/careers
// @access  Private/Admin
const createCareer = async (req, res) => {
    const { job_title, job_description } = req.body;
    const thumbnail = req.file ? `/uploads/careers/${req.file.filename}` : null;
    
    try {
        if (!(await ensureUniqueTitle(job_title))) {
            cleanupUploadedFile(req);
            return conflictError(res, 'job_title', 'Career title already exists');
        }

        const result = await sequelize.query(
            'INSERT INTO careers (thumbnail, job_title, job_description, createdAt, updatedAt) VALUES (?, ?, ?, NOW(), NOW())',
            {
                replacements: [thumbnail, job_title, job_description],
                type: QueryTypes.INSERT
            }
        );
        res.status(201).json({ id: result[0], thumbnail, job_title, job_description });
    } catch (error) {
        cleanupUploadedFile(req);
        serverError(res, error);
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
            cleanupUploadedFile(req);
            return notFound(res, 'Career not found');
        }

        if (job_title && !(await ensureUniqueTitle(job_title, id))) {
            cleanupUploadedFile(req);
            return conflictError(res, 'job_title', 'Career title already exists');
        }

        if (req.file) {
            removeFile(existingCareer[0].thumbnail);
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
        cleanupUploadedFile(req);
        serverError(res, error);
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
            removeFile(career.thumbnail);
        } else {
            return notFound(res, 'Career not found');
        }

        await sequelize.query(
            'DELETE FROM careers WHERE id = ?',
            { replacements: [req.params.id], type: QueryTypes.DELETE }
        );
        res.json({ message: 'Career removed' });
    } catch (error) {
        serverError(res, error);
    }
};

module.exports = {
    getAllCareers,
    submitCareerApplication,
    createCareer,
    updateCareer,
    deleteCareer
};
