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
const { createId } = require('../utils/id');
const { getPagination, sendListResponse } = require('../utils/pagination');

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
        const pagination = getPagination(req.query);
        const replacements = [];
        const where = [];
        const search = String(req.query.search || '').trim();
        const category = String(req.query.category || '').trim();

        if (search) {
            const keyword = `%${search}%`;
            where.push('(careers.job_title LIKE ? OR careers.job_description LIKE ? OR categories.name LIKE ?)');
            replacements.push(keyword, keyword, keyword);
        }

        if (req.query.categoryId === 'unassigned') {
            where.push('careers.categoryId IS NULL');
        } else if (req.query.categoryId && req.query.categoryId !== 'all') {
            where.push('careers.categoryId = ?');
            replacements.push(req.query.categoryId);
        }

        if (category && category !== 'all') {
            where.push('LOWER(categories.name) = LOWER(?)');
            replacements.push(category);
        }

        const whereClause = where.length ? `WHERE ${where.join(' AND ')}` : '';
        const countResult = pagination
            ? await sequelize.query(
                `SELECT COUNT(*) AS total
                 FROM careers
                 LEFT JOIN categories ON careers.categoryId = categories.id
                 ${whereClause}`,
                { replacements, type: QueryTypes.SELECT }
            )
            : [{ total: 0 }];
        const dataReplacements = [...replacements];
        const paginationSql = pagination ? ' LIMIT ? OFFSET ?' : '';
        if (pagination) dataReplacements.push(pagination.limit, pagination.offset);
        const careers = await sequelize.query(
            `SELECT careers.*, categories.name AS category_name
             FROM careers
             LEFT JOIN categories ON careers.categoryId = categories.id
             ${whereClause}
             ORDER BY job_title ASC${paginationSql}`,
            { replacements: dataReplacements, type: QueryTypes.SELECT }
        );
        sendListResponse(res, careers, pagination, countResult[0]?.total);
    } catch (error) {
        serverError(res, error);
    }
};

// @desc    Get career applications
// @route   GET /api/careers/applications
// @access  Private/Admin
const getCareerApplications = async (req, res) => {
    try {
        const pagination = getPagination(req.query);
        const replacements = [];
        const where = [];
        const search = String(req.query.search || '').trim();
        const status = String(req.query.status || '').trim();

        if (search) {
            const keyword = `%${search}%`;
            where.push('(full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR position LIKE ? OR latest_education LIKE ?)');
            replacements.push(keyword, keyword, keyword, keyword, keyword);
        }

        if (status && status !== 'all') {
            where.push('LOWER(status) = LOWER(?)');
            replacements.push(status);
        }

        const whereClause = where.length ? `WHERE ${where.join(' AND ')}` : '';
        const countResult = pagination
            ? await sequelize.query(`SELECT COUNT(*) AS total FROM career_applications ${whereClause}`, { replacements, type: QueryTypes.SELECT })
            : [{ total: 0 }];
        const dataReplacements = [...replacements];
        const paginationSql = pagination ? ' LIMIT ? OFFSET ?' : '';
        if (pagination) dataReplacements.push(pagination.limit, pagination.offset);
        const applications = await sequelize.query(
            `SELECT * FROM career_applications ${whereClause} ORDER BY createdAt DESC${paginationSql}`,
            { replacements: dataReplacements, type: QueryTypes.SELECT }
        );

        const applicationsWithResumeLinks = await Promise.all(applications.map(async (application) => {
            if (!application.cv_storage_key) return application;

            try {
                return {
                    ...application,
                    cv_signed_url: await getSignedObjectUrl(application.cv_storage_key),
                };
            } catch (signedUrlError) {
                if (process.env.NODE_ENV !== 'production') {
                    console.error('Failed to create application resume signed URL:', signedUrlError.message);
                }

                return application;
            }
        }));

        sendListResponse(res, applicationsWithResumeLinks, pagination, countResult[0]?.total);
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
            message: 'Please upload your resume as a PDF.',
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
            message: 'Your application has been submitted successfully. Please wait while our team reviews your submission. We will contact you by email.',
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
                message: 'Object storage is not configured yet. Please complete the storage environment variables first.',
            });
        }

        if (isStorageProviderError(error)) {
            if (process.env.NODE_ENV !== 'production') {
                console.error('Object storage upload failed:', error.Code || error.code || error.message);
            }

            return res.status(502).json({
                success: false,
                message: 'Resume upload to storage failed. Please check the Supabase Storage configuration.',
            });
        }

        serverError(res, error, 'We could not submit your application. Please try again in a moment.');
    }
};

// @desc    Create career
// @route   POST /api/careers
// @access  Private/Admin
const createCareer = async (req, res) => {
    const { job_title, job_description, categoryId } = req.body;
    const thumbnail = req.file ? `/uploads/careers/${req.file.filename}` : null;
    
    try {
        if (!(await ensureUniqueTitle(job_title))) {
            cleanupUploadedFile(req);
            return conflictError(res, 'job_title', 'Career title already exists');
        }

        const id = createId();
        await sequelize.query(
            'INSERT INTO careers (id, thumbnail, job_title, job_description, categoryId, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
            {
                replacements: [id, thumbnail, job_title, job_description, categoryId || null],
                type: QueryTypes.INSERT
            }
        );
        res.status(201).json({ id, thumbnail, job_title, job_description, categoryId: categoryId || null });
    } catch (error) {
        cleanupUploadedFile(req);
        serverError(res, error);
    }
};

// @desc    Update career
// @route   PUT /api/careers/:id
// @access  Private/Admin
const updateCareer = async (req, res) => {
    const { job_title, job_description, categoryId } = req.body;
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
            'UPDATE careers SET thumbnail = ?, job_title = ?, job_description = ?, categoryId = ?, updatedAt = NOW() WHERE id = ?',
            {
                replacements: [thumbnail, job_title, job_description, categoryId || null, id],
                type: QueryTypes.UPDATE
            }
        );
        res.json({ id, thumbnail, job_title, job_description, categoryId: categoryId || null, message: 'Career updated successfully' });
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
    getCareerApplications,
    submitCareerApplication,
    createCareer,
    updateCareer,
    deleteCareer
};
