const { sequelize } = require('../config/database');
const { QueryTypes } = require('sequelize');

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
    res.status(201).json({ id: result, name, email, subject, message });
  } catch (error) {
    res.status(500).json({ message: error.message });
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
    res.status(500).json({ message: error.message });
  }
};

// @desc    Mark message as read
// @route   PUT /api/contact/:id/read
// @access  Private/Admin
const markAsRead = async (req, res) => {
  try {
    await sequelize.query(
      'UPDATE contact_messages SET is_read = 1, updatedAt = NOW() WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.UPDATE }
    );
    res.json({ message: 'Message marked as read' });
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// @desc    Delete message
// @route   DELETE /api/contact/:id
// @access  Private/Admin
const deleteMessage = async (req, res) => {
  try {
    await sequelize.query(
      'DELETE FROM contact_messages WHERE id = ?',
      { replacements: [req.params.id], type: QueryTypes.DELETE }
    );
    res.json({ message: 'Message removed' });
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

module.exports = { submitMessage, getMessages, markAsRead, deleteMessage };
