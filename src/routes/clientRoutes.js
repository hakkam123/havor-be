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
const { validate } = require('../middlewares/securityMiddleware');
const schemas = require('../validations/requestSchemas');

router.get('/', getAllClients);

router.post('/', protect, upload.single('client_icon'), validate(schemas.client.create), createClient);
router.put('/:id', protect, validate(schemas.idParam, 'params'), upload.single('client_icon'), validate(schemas.client.update), updateClient);
router.delete('/:id', protect, validate(schemas.idParam, 'params'), deleteClient);

module.exports = router;
