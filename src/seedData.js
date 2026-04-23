const { sequelize, connectDB } = require('./config/database');
const News = require('./models/News');
const HeroBanner = require('./models/HeroBanner');
const Expertise = require('./models/Expertise');
const Product = require('./models/Product');
const Work = require('./models/Work');
const ContactMessage = require('./models/ContactMessage');
const Client = require('./models/Client');
const Career = require('./models/Career');
const fs = require('fs');
const path = require('path');
const slugify = require('slugify');
require('dotenv').config();

const uploadsDir = path.join(__dirname, '..', 'uploads');

const getUploadFiles = (folderName) => {
  const folderPath = path.join(uploadsDir, folderName);

  if (!fs.existsSync(folderPath)) {
    return [];
  }

  return fs.readdirSync(folderPath)
    .filter((file) => fs.statSync(path.join(folderPath, file)).isFile())
    .map((file) => `/uploads/${folderName}/${file}`);
};

const pick = (items, fallback = null) => items.length > 0 ? items : [fallback].filter(Boolean);

const buildNewsSeeds = (newsImages) => [
  {
    title: 'Havor Smarta Technology Launches New AI Solutions',
    slug: slugify('Havor Smarta Technology Launches New AI Solutions', { lower: true, strict: true }),
    content: 'We are proud to announce our latest AI-driven solutions for international logistics...',
    category: 'Technology',
    is_published: true,
    image_url: newsImages[0] || null
  },
  {
    title: 'Expansion into European Markets',
    slug: slugify('Expansion into European Markets', { lower: true, strict: true }),
    content: 'PT Havor Smarta Technology is officially opening its first European branch in Berlin...',
    category: 'Corporate',
    is_published: true,
    image_url: newsImages[1] || newsImages[0] || null
  }
];

const buildBannerSeeds = (bannerMedia) => [
  {
    page_name: 'home',
    title: 'Innovating for a Smarter Future',
    subtitle: 'Global Technology Solutions at Your Fingertips',
    media_url: bannerMedia[0] || null,
    media_type: bannerMedia[0] && path.extname(bannerMedia[0]).toLowerCase() === '.mp4' ? 'video' : 'image'
  },
  {
    page_name: 'about',
    title: 'About PT Havor Smarta',
    subtitle: 'Our journey to becoming a global tech leader',
    media_url: bannerMedia[1] || bannerMedia[0] || null,
    media_type: (bannerMedia[1] || bannerMedia[0]) && path.extname((bannerMedia[1] || bannerMedia[0])).toLowerCase() === '.mp4' ? 'video' : 'image'
  }
].filter((item) => item.media_url);

const buildExpertiseSeeds = (expertiseIcons) => [
  {
    name: 'Software Development',
    description: 'Scalable and robust custom software solutions for enterprise needs.',
    icon_url: expertiseIcons[0] || null
  },
  {
    name: 'AI & Data Analytics',
    description: 'Leveraging data to drive intelligent business decisions.',
    icon_url: expertiseIcons[1] || expertiseIcons[0] || null
  }
];

const buildProductSeeds = (productImages) => [
  {
    name: 'Havor ERP v1.0',
    description: 'Enterprise resource planning for international trade.',
    image_url: productImages[0] || null,
    external_link: 'https://havor.com/erp'
  },
  {
    name: 'SecureGate VPN',
    description: 'High-security networking for global teams.',
    image_url: productImages[1] || productImages[0] || null,
    external_link: 'https://havor.com/securegate'
  }
];

const buildWorkSeeds = (workImages) => [
  {
    title: 'Global Logistics Dashboard',
    category: 'App Development',
    description: 'A real-time tracking system for global shipping routes.',
    image_url: workImages[0] || null,
    client: 'Global Ship Inc.',
    year: 2025
  },
  {
    title: 'Smart City Infrastructure',
    category: 'UI/UX Design',
    description: 'Interface design for metropolitan traffic management.',
    image_url: workImages[1] || workImages[0] || null,
    client: 'Singapore GOV',
    year: 2024
  }
];

const buildClientSeeds = (clientIcons) => [
  {
    name: 'Global Partner',
    client_icon: clientIcons[0] || null,
    description: 'Long-term technology partner'
  },
  {
    name: 'Enterprise Client',
    client_icon: clientIcons[1] || clientIcons[0] || null,
    description: 'Strategic implementation client'
  }
];

const buildCareerSeeds = (careerThumbs) => [
  {
    thumbnail: careerThumbs[0] || null,
    job_title: 'Frontend Developer',
    job_description: 'Build and maintain web interfaces'
  },
  {
    thumbnail: careerThumbs[1] || careerThumbs[0] || null,
    job_title: 'UI Designer',
    job_description: 'Design modern digital experiences'
  }
];

const seedDummyData = async () => {
  try {
    await connectDB();
    await sequelize.sync();

    console.log('🌱 Starting Seeder...');

    const newsImages = getUploadFiles('news');
    const bannerMedia = getUploadFiles('banners');
    const expertiseIcons = getUploadFiles('expertise');
    const productImages = getUploadFiles('products');
    const workImages = getUploadFiles('works');
    const clientIcons = getUploadFiles('clients');
    const careerThumbs = getUploadFiles('careers');

    // 1. News
    await News.bulkCreate(buildNewsSeeds(newsImages));
    console.log('✅ News Seeded');

    // 2. Hero Banners
    if (bannerMedia.length > 0) {
      await HeroBanner.bulkCreate(buildBannerSeeds(bannerMedia));
    }
    console.log('✅ Banners Seeded');

    // 3. Expertise
    await Expertise.bulkCreate(buildExpertiseSeeds(expertiseIcons));
    console.log('✅ Expertise Seeded');

    // 4. Products
    await Product.bulkCreate(buildProductSeeds(productImages));
    console.log('✅ Products Seeded');

    // 5. Works
    await Work.bulkCreate(buildWorkSeeds(workImages));
    console.log('✅ Works Seeded');

    // 6. Clients
    await Client.bulkCreate(buildClientSeeds(clientIcons));
    console.log('✅ Clients Seeded');

    // 7. Careers
    await Career.bulkCreate(buildCareerSeeds(careerThumbs));
    console.log('✅ Careers Seeded');

    // 8. Contact Messages
    await ContactMessage.bulkCreate([
      {
        name: 'John Doe',
        email: 'john@example.com',
        subject: 'Business Inquiry',
        message: 'Hello, we are interested in your AI solutions. Let us talk.',
        is_read: false
      },
      {
        name: 'Jane Smith',
        email: 'jane@worldtech.com',
        subject: 'Partnership',
        message: 'We would like to partner with Havor for European distribution.',
        is_read: true
      }
    ]);
    console.log('✅ Contact Messages Seeded');

    console.log('🏁 All Data Seeded Successfully!');
    process.exit();
  } catch (error) {
    console.error('❌ Error seeding data:', error.message);
    process.exit(1);
  }
};

seedDummyData();
