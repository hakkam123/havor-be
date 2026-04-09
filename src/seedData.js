const { sequelize, connectDB } = require('./config/database');
const News = require('./models/News');
const HeroBanner = require('./models/HeroBanner');
const Expertise = require('./models/Expertise');
const Product = require('./models/Product');
const Work = require('./models/Work');
const ContactMessage = require('./models/ContactMessage');
require('dotenv').config();

const seedDummyData = async () => {
  try {
    await connectDB();
    await sequelize.sync();

    console.log('🌱 Starting Seeder...');

    // 1. News
    await News.bulkCreate([
      {
        title: 'Havor Smarta Technology Launches New AI Solutions',
        content: 'We are proud to announce our latest AI-driven solutions for international logistics...',
        category: 'Technology',
        is_published: true,
        image_url: 'https://picsum.photos/800/600?random=1'
      },
      {
        title: 'Expansion into European Markets',
        content: 'PT Havor Smarta Technology is officially opening its first European branch in Berlin...',
        category: 'Corporate',
        is_published: true,
        image_url: 'https://picsum.photos/800/600?random=2'
      }
    ]);
    console.log('✅ News Seeded');

    // 2. Hero Banners
    await HeroBanner.bulkCreate([
      {
        page_name: 'home',
        title: 'Innovating for a Smarter Future',
        subtitle: 'Global Technology Solutions at Your Fingertips',
        media_url: 'https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4',
        media_type: 'video'
      },
      {
        page_name: 'about',
        title: 'About PT Havor Smarta',
        subtitle: 'Our journey to becoming a global tech leader',
        media_url: 'https://picsum.photos/1920/1080?random=3',
        media_type: 'image'
      }
    ]);
    console.log('✅ Banners Seeded');

    // 3. Expertise
    await Expertise.bulkCreate([
      {
        name: 'Software Development',
        description: 'Scalable and robust custom software solutions for enterprise needs.',
        icon_url: 'https://picsum.photos/100/100?random=4'
      },
      {
        name: 'AI & Data Analytics',
        description: 'Leveraging data to drive intelligent business decisions.',
        icon_url: 'https://picsum.photos/100/100?random=5'
      }
    ]);
    console.log('✅ Expertise Seeded');

    // 4. Products
    await Product.bulkCreate([
      {
        name: 'Havor ERP v1.0',
        description: 'Enterprise resource planning for international trade.',
        image_url: 'https://picsum.photos/400/300?random=6',
        external_link: 'https://havor.com/erp'
      },
      {
        name: 'SecureGate VPN',
        description: 'High-security networking for global teams.',
        image_url: 'https://picsum.photos/400/300?random=7',
        external_link: 'https://havor.com/securegate'
      }
    ]);
    console.log('✅ Products Seeded');

    // 5. Works
    await Work.bulkCreate([
      {
        title: 'Global Logistics Dashboard',
        category: 'App Development',
        description: 'A real-time tracking system for global shipping routes.',
        image_url: 'https://picsum.photos/600/400?random=8',
        client: 'Global Ship Inc.',
        year: 2025
      },
      {
        title: 'Smart City Infrastructure',
        category: 'UI/UX Design',
        description: 'Interface design for metropolitan traffic management.',
        image_url: 'https://picsum.photos/600/400?random=9',
        client: 'Singapore GOV',
        year: 2024
      }
    ]);
    console.log('✅ Works Seeded');

    // 6. Contact Messages
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
