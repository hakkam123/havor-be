const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');
const { sendContactEmails } = require('../services/emailService');
const { notFound, serverError } = require('../utils/apiResponse');

// @desc    Submit contact message
// @route   POST /api/contact
// @access  Public
const submitMessage = async (req, res) => {
  const { name, email, subject, message } = req.body;
  try {
    const [result] = await sequelize.query(
      `INSERT INTO contact_messages (name, email, subject, message, createdAt, updatedAt) 
       VALUES (?, ?, ?, ?, NOW(), NOW())`,
      {
        replacements: [name, email, subject, message],
        type: QueryTypes.INSERT
      }
    );

    const emailStatus = await sendContactEmails({
      message: { name, email, subject, message },
    });

    res.status(201).json({
      success: true,
      message: 'Your message has been sent successfully. Please wait while our team reviews your submission. We will contact you by email.',
      data: {
        id: result,
        email: {
          sender: emailStatus.userEmail.sent,
          admin: emailStatus.adminEmail.sent,
        },
      },
    });
  } catch (error) {
    serverError(res, error, 'We could not submit your message. Please try again in a moment.');
  }
};

// @desc    Get all messages
// @route   GET /api/contact
// @access  Private/Admin
const getMessages = async (req, res) => {
  try {
    const messages = await sequelize.query(
      'SELECT * FROM contact_messages ORDER BY createdAt DESC',
      { type: QueryTypes.SELECT }
    );
    res.json(messages);
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Mark message as read
// @route   PUT /api/contact/:id/read
// @access  Private/Admin
const markAsRead = async (req, res) => {
  try {
    const existing = await sequelize.query(
      'SELECT id FROM contact_messages WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );
    if (existing.length === 0) {
      return notFound(res, 'Message not found');
    }

    await sequelize.query(
      'UPDATE contact_messages SET is_read = 1, updatedAt = NOW() WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.UPDATE }
    );
    res.json({ message: 'Message marked as read' });
  } catch (error) {
    serverError(res, error);
  }
};

// @desc    Delete message
// @route   DELETE /api/contact/:id
// @access  Private/Admin
const deleteMessage = async (req, res) => {
  try {
    const existing = await sequelize.query(
      'SELECT id FROM contact_messages WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.SELECT }
    );
    if (existing.length === 0) {
      return notFound(res, 'Message not found');
    }

    await sequelize.query(
      'DELETE FROM contact_messages WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.DELETE }
    );
    res.json({ message: 'Message removed' });
  } catch (error) {
    serverError(res, error);
  }
};

module.exports = { submitMessage, getMessages, markAsRead, deleteMessage };
