const express = require('express');
const router = express.Router();
const {
    getAllClients,
    createClient,   
    updateClient,
    deleteClient
} = require('../controllers/clientController');
const { protect } = require('../middlewares/authMiddleware');
const { upload } = require('../middlewares/uploadMiddleware');

router.get('/', getAllClients);

router.post('/', protect, upload.single('client_icon'), createClient);
router.put('/:id', protect, upload.single('client_icon'), updateClient);
router.delete('/:id', protect, deleteClient);

module.exports = router;