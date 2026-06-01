const CompanyProfile = require('../models/CompanyProfile');
const {
  cleanupUploadedFile,
  removeFile,
  serverError,
} = require('../utils/apiResponse');

const defaultProfile = {
  company_name: 'Havor Smarta Digital',
  tagline: 'Your Digital IT Partner Solution',
  short_description: 'Integrated technology solutions for web, mobile, enterprise systems, dashboards, and intelligent digital platforms.',
  long_description: 'Havor Smarta Digital is an Information Technology company specializing in digital solutions and application development for business growth and digital transformation.',
  email: 'bisnis@havorsmartadigital.com',
  phone: '+62-813-8036-2223',
  website: 'https://www.havorsmartadigital.com',
  address: 'Rukan Andalan, Jl. Asem Baris Raya No 15C, Tebet Jakarta Selatan',
  linkedin_url: '',
  instagram_url: '',
  logo_url: '',
  seo_title: 'Havor Smarta Digital - Your Digital IT Partner Solution',
  seo_description: 'Havor Smarta Digital provides application development, websites, mobile apps, enterprise IT solutions, and intelligent systems.',
};

const findOrCreateProfile = async () => {
  const profile = await CompanyProfile.findOne({ order: [['id', 'ASC']] });
  if (profile) return profile;
  return CompanyProfile.create(defaultProfile);
};

const getProfile = async (req, res) => {
  try {
    const profile = await findOrCreateProfile();
    res.json(profile);
  } catch (error) {
    serverError(res, error);
  }
};

const updateProfile = async (req, res) => {
  try {
    const profile = await findOrCreateProfile();
    const nextData = { ...req.body };

    if (req.file) {
      removeFile(profile.logo_url);
      nextData.logo_url = `/uploads/profile/${req.file.filename}`;
    }

    await profile.update(nextData);
    res.json(profile);
  } catch (error) {
    cleanupUploadedFile(req);
    serverError(res, error);
  }
};

module.exports = { getProfile, updateProfile };
