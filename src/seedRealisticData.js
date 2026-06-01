const fs = require('fs');
const path = require('path');
const slugify = require('slugify');
const { connectDB, sequelize } = require('./config/database');
const HeroBanner = require('./models/HeroBanner');
const Category = require('./models/Category');
const Client = require('./models/Client');
const Expertise = require('./models/Expertise');
const Work = require('./models/Work');
const News = require('./models/News');
const Product = require('./models/Product');
const Career = require('./models/Career');
const ContactMessage = require('./models/ContactMessage');
const CompanyProfile = require('./models/CompanyProfile');

require('dotenv').config();

const projectRoot = path.join(__dirname, '..');
const uploadRoot = path.join(projectRoot, 'uploads');
const seedAssetRoot = path.join(projectRoot, 'seed-assets');

const slug = (value) => slugify(value, { lower: true, strict: true });

const copyAsset = (folder, fileName) => {
  const sourcePath = path.join(seedAssetRoot, folder, fileName);
  const targetFolder = path.join(uploadRoot, folder);
  const targetName = `havor-sample-${fileName}`;
  const targetPath = path.join(targetFolder, targetName);

  if (!fs.existsSync(sourcePath)) {
    return null;
  }

  fs.mkdirSync(targetFolder, { recursive: true });
  fs.copyFileSync(sourcePath, targetPath);

  return `/uploads/${folder}/${targetName}`;
};

const upsertBy = async (Model, where, values) => {
  const existing = await Model.findOne({ where });
  if (existing) {
    await existing.update(values);
    return existing;
  }

  return Model.create({ ...where, ...values });
};

const longArticle = (title, focus) => `
<h2>${title}</h2>
<p>${focus} has become a practical priority for companies that want digital systems to support real business activity, not only visual presentation. Many organizations already use websites, forms, spreadsheets, messaging groups, and separate tools, but the information often moves slowly because every team stores context in a different place. A well-planned digital platform helps leadership, operations, marketing, and service teams work from the same source of truth while keeping each workflow clear and measurable.</p>
<p>For Havor Smarta Digital, the most important part of a digital initiative is understanding the business process before choosing the technology stack. A company profile website, CMS, dashboard, mobile application, or enterprise system should be designed around the way people actually work. This includes the approval flow, reporting needs, user roles, content ownership, data structure, and future maintenance model. When these details are mapped early, the system becomes easier to scale and easier for internal teams to adopt.</p>
<p>A strong implementation also needs clear separation between content, application logic, and presentation. Content teams should be able to update articles, services, projects, career openings, and landing page material without asking developers for every small change. Technical teams, meanwhile, need predictable API responses, validation, upload handling, authentication, and deployment practices. This balance keeps the platform stable while allowing business teams to move faster.</p>
<p>Responsive design is another important part of the same conversation. Users may open the platform from a large desktop monitor, a tablet during meetings, or a mobile device while working outside the office. Layout decisions, image sizes, typography, and navigation behavior must support all of those contexts. A system that looks polished but breaks on smaller screens will still create friction for the audience and reduce confidence in the brand.</p>
<p>Security and data quality should not be treated as later-stage improvements. Input validation, protected dashboard routes, safe file uploads, structured error messages, and draft or unpublished content rules must be part of the core implementation. These details reduce operational risk and prevent admin mistakes from becoming public-facing problems. They also make testing more reliable because every module responds consistently when invalid data is submitted.</p>
<p>The practical value of ${focus.toLowerCase()} appears when teams can manage information faster, review performance more clearly, and continue improving the system after launch. A good digital partner does not only build screens. The partner helps shape a maintainable workflow, creates a dependable foundation, and supports the organization as needs evolve. This is the delivery mindset that Havor brings to web platforms, dashboards, CMS products, mobile applications, and enterprise systems.</p>
`;

const seed = async () => {
  await connectDB();
  await sequelize.sync();

  const categories = await Promise.all([
    upsertBy(Category, { name: 'Company Profile' }, {}),
    upsertBy(Category, { name: 'Information System' }, {}),
    upsertBy(Category, { name: 'Dashboard Analytics' }, {}),
    upsertBy(Category, { name: 'Web Application' }, {}),
    upsertBy(Category, { name: 'Digital Product' }, {}),
  ]);

  const categoryByName = Object.fromEntries(categories.map((item) => [item.name, item]));

  await Promise.all([
    upsertBy(HeroBanner, { page_name: 'home' }, {
      title: 'Digital Transformation for Growing Businesses',
      subtitle: 'Integrated websites, applications, dashboards, and intelligent systems for practical business growth.',
      media_url: copyAsset('banners', 'digital-transformation.png'),
      media_type: 'image',
    }),
    upsertBy(HeroBanner, { page_name: 'services' }, {
      title: 'Scalable Web Development Solutions',
      subtitle: 'Build modern platforms with clean UX, secure APIs, and maintainable content management.',
      media_url: copyAsset('banners', 'web-development.png'),
      media_type: 'image',
    }),
    upsertBy(HeroBanner, { page_name: 'projects' }, {
      title: 'Project Delivery for Business Systems',
      subtitle: 'From company profile websites to dashboards and internal applications.',
      media_url: copyAsset('banners', 'project-delivery.png'),
      media_type: 'image',
    }),
    upsertBy(HeroBanner, { page_name: 'media-news' }, {
      title: 'Technology Insights and Company Updates',
      subtitle: 'Practical perspectives on digital delivery, CMS, dashboards, and application development.',
      media_url: copyAsset('banners', 'technology-insights.png'),
      media_type: 'image',
    }),
    upsertBy(HeroBanner, { page_name: 'careers' }, {
      title: 'Build Practical Digital Products with Havor',
      subtitle: 'Join a team focused on clean implementation, collaboration, and business impact.',
      media_url: copyAsset('banners', 'careers-team.png'),
      media_type: 'image',
    }),
  ]);

  await Promise.all([
    upsertBy(Client, { name: 'PT Nusantara Digital Operasi' }, {
      client_icon: copyAsset('clients', 'nusantara-digital.png'),
      description: 'Operations-focused company that needed stronger visibility across internal service and reporting workflows.',
    }),
    upsertBy(Client, { name: 'CV Arunika Kreatif Teknologi' }, {
      client_icon: copyAsset('clients', 'arunika-kreatif.png'),
      description: 'Creative technology partner managing content, campaign microsites, and business presentation platforms.',
    }),
    upsertBy(Client, { name: 'Raja Laut Regional Service Unit' }, {
      client_icon: copyAsset('clients', 'raja-laut.png'),
      description: 'Regional service organization requiring structured information flow and responsive public communication.',
    }),
    upsertBy(Client, { name: 'Bogor Public Information Center' }, {
      client_icon: copyAsset('clients', 'bogor-info-center.png'),
      description: 'Public information unit focused on accessible digital content and reliable publication management.',
    }),
    upsertBy(Client, { name: 'Smart Commerce Indonesia' }, {
      client_icon: copyAsset('clients', 'smart-commerce.png'),
      description: 'Commerce operator improving catalog, admin dashboard, and transaction monitoring capability.',
    }),
  ]);

  await Promise.all([
    upsertBy(Expertise, { name: 'Full-stack Web Development' }, {
      icon_url: copyAsset('expertise', 'fullstack-web.png'),
      description: 'End-to-end web application development covering UI implementation, backend APIs, authentication, dashboard modules, and deployment readiness.',
    }),
    upsertBy(Expertise, { name: 'Company Profile Website' }, {
      icon_url: copyAsset('expertise', 'company-profile.png'),
      description: 'Corporate website development with clear content hierarchy, responsive layouts, CMS-ready sections, SEO foundations, and professional brand presentation.',
    }),
    upsertBy(Expertise, { name: 'Custom CMS Development' }, {
      icon_url: copyAsset('expertise', 'custom-cms.png'),
      description: 'Content management systems for services, works, news, careers, banners, clients, and company profile information with secure admin workflows.',
    }),
    upsertBy(Expertise, { name: 'Dashboard and Data Visualization' }, {
      icon_url: copyAsset('expertise', 'dashboard-data.png'),
      description: 'Operational dashboards that transform business data into readable summaries, charts, filters, and decision-support views.',
    }),
    upsertBy(Expertise, { name: 'API Integration and Backend Development' }, {
      icon_url: copyAsset('expertise', 'api-integration.png'),
      description: 'Backend services, database design, validation, upload handling, third-party integrations, and API contracts for scalable digital products.',
    }),
  ]);

  await Promise.all([
    upsertBy(Work, { title: 'SIJALA Real-time Marine Report Portal' }, {
      categoryId: categoryByName['Information System'].id,
      client: 'Raja Laut Regional Service Unit',
      year: 2026,
      image_url: copyAsset('works', 'sijala-marine-portal.png'),
      description: '<h3>Problem</h3><p>Regional teams needed a clear way to publish and monitor marine activity reports without relying on scattered spreadsheets and messaging groups.</p><h3>Solution</h3><p>Havor designed a responsive reporting portal with structured content, dashboard summaries, role-based administration, and public information pages.</p><h3>Result</h3><p>The organization gained cleaner report visibility, faster information updates, and a stronger foundation for future operational dashboards.</p>',
    }),
    upsertBy(Work, { title: 'BPBD Disaster Information Landing Page' }, {
      categoryId: categoryByName['Company Profile'].id,
      client: 'Bogor Public Information Center',
      year: 2026,
      image_url: copyAsset('works', 'bpbd-landing-page.png'),
      description: '<h3>Problem</h3><p>The public information team required a clear landing page to communicate service priorities, emergency resources, and official updates.</p><h3>Solution</h3><p>Havor built a corporate public-service layout with strong image sections, accessible typography, and admin-managed news content.</p><h3>Result</h3><p>The page became easier to scan, easier to maintain, and more consistent across desktop and mobile users.</p>',
    }),
    upsertBy(Work, { title: 'Company Profile CMS for Digital Agency' }, {
      categoryId: categoryByName['Web Application'].id,
      client: 'CV Arunika Kreatif Teknologi',
      year: 2025,
      image_url: copyAsset('works', 'agency-cms.png'),
      description: '<h3>Problem</h3><p>The agency needed to manage services, project portfolios, banners, and newsroom content from one simple dashboard.</p><h3>Solution</h3><p>Havor implemented a CMS workflow with CRUD modules, upload support, public synchronization, and polished landing page presentation.</p><h3>Result</h3><p>The internal team could update the website faster while keeping public sections consistent and professional.</p>',
    }),
    upsertBy(Work, { title: 'QR Code Attendance Management System' }, {
      categoryId: categoryByName['Information System'].id,
      client: 'PT Nusantara Digital Operasi',
      year: 2025,
      image_url: copyAsset('works', 'qr-attendance.png'),
      description: '<h3>Problem</h3><p>Attendance tracking relied on manual recap and late validation, making operational reports difficult to trust.</p><h3>Solution</h3><p>Havor delivered an attendance workflow using QR check-in, admin monitoring, report exports, and user-friendly mobile access.</p><h3>Result</h3><p>Daily attendance visibility improved and the team reduced repeated manual recap work.</p>',
    }),
    upsertBy(Work, { title: 'Marketplace Admin Management Dashboard' }, {
      categoryId: categoryByName['Dashboard Analytics'].id,
      client: 'Smart Commerce Indonesia',
      year: 2026,
      image_url: copyAsset('works', 'marketplace-dashboard.png'),
      description: '<h3>Problem</h3><p>The commerce team needed faster access to catalog status, order activity, and operational indicators.</p><h3>Solution</h3><p>Havor built a dashboard concept with catalog controls, data summaries, category filters, and responsive admin interactions.</p><h3>Result</h3><p>The business gained a clearer operational command center for daily commerce management.</p>',
    }),
  ]);

  await Promise.all([
    upsertBy(News, { slug: slug('How a CMS Helps Companies Manage Digital Content More Efficiently') }, {
      title: 'How a CMS Helps Companies Manage Digital Content More Efficiently',
      content: longArticle('How a CMS Helps Companies Manage Digital Content More Efficiently', 'A structured content management system'),
      category: 'Technology',
      is_published: true,
      image_url: copyAsset('news', 'cms-efficiency.png'),
    }),
    upsertBy(News, { slug: slug('Why Real-time Reporting Matters for Public Information Systems') }, {
      title: 'Why Real-time Reporting Matters for Public Information Systems',
      content: longArticle('Why Real-time Reporting Matters for Public Information Systems', 'Real-time reporting'),
      category: 'Public Systems',
      is_published: true,
      image_url: copyAsset('news', 'realtime-reporting.png'),
    }),
    upsertBy(News, { slug: slug('The Role of Dashboard Analytics in Operational Decision-Making') }, {
      title: 'The Role of Dashboard Analytics in Operational Decision-Making',
      content: longArticle('The Role of Dashboard Analytics in Operational Decision-Making', 'Dashboard analytics'),
      category: 'Data Insight',
      is_published: true,
      image_url: copyAsset('news', 'dashboard-analytics.png'),
    }),
    upsertBy(News, { slug: slug('Building Scalable Web Applications with API-First Architecture') }, {
      title: 'Building Scalable Web Applications with API-First Architecture',
      content: longArticle('Building Scalable Web Applications with API-First Architecture', 'API-first architecture'),
      category: 'Engineering',
      is_published: true,
      image_url: copyAsset('news', 'api-first.png'),
    }),
    upsertBy(News, { slug: slug('Improving User Experience Through Structured Content and Responsive Design') }, {
      title: 'Improving User Experience Through Structured Content and Responsive Design',
      content: longArticle('Improving User Experience Through Structured Content and Responsive Design', 'Structured content and responsive design'),
      category: 'UX Design',
      is_published: true,
      image_url: copyAsset('news', 'responsive-ux.png'),
    }),
  ]);

  await Promise.all([
    upsertBy(Product, { name: 'Havor CMS Starter' }, {
      categoryId: categoryByName['Digital Product'].id,
      image_url: copyAsset('products', 'havor-cms-starter.png'),
      external_link: 'https://www.havorsmartadigital.com/products/havor-cms-starter',
      description: 'A starter CMS package for company profile websites, including service management, portfolio content, news publishing, career postings, and banner controls.',
    }),
    upsertBy(Product, { name: 'Attendance QR System' }, {
      categoryId: categoryByName['Information System'].id,
      image_url: copyAsset('products', 'attendance-qr.png'),
      external_link: 'https://www.havorsmartadigital.com/products/attendance-qr-system',
      description: 'A QR-based attendance system for teams that need practical check-in workflows, admin monitoring, and report visibility.',
    }),
    upsertBy(Product, { name: 'Reporting Dashboard Kit' }, {
      categoryId: categoryByName['Dashboard Analytics'].id,
      image_url: copyAsset('products', 'reporting-dashboard-kit.png'),
      external_link: 'https://www.havorsmartadigital.com/products/reporting-dashboard-kit',
      description: 'A dashboard implementation starter for operational summaries, filters, chart views, and reporting workflows.',
    }),
    upsertBy(Product, { name: 'Company Profile Website Package' }, {
      categoryId: categoryByName['Company Profile'].id,
      image_url: copyAsset('products', 'company-profile-package.png'),
      external_link: 'https://www.havorsmartadigital.com/products/company-profile-website-package',
      description: 'A professional website package for organizations that need clean corporate presentation, CMS-managed sections, and responsive page structure.',
    }),
    upsertBy(Product, { name: 'API Integration Starter Pack' }, {
      categoryId: categoryByName['Web Application'].id,
      image_url: copyAsset('products', 'api-integration-pack.png'),
      external_link: 'https://www.havorsmartadigital.com/products/api-integration-starter-pack',
      description: 'A backend integration starter for projects that need reliable API contracts, validation, third-party service connection, and secure data flow.',
    }),
  ]);

  await Promise.all([
    upsertBy(Career, { job_title: 'Frontend Developer Intern' }, {
      thumbnail: copyAsset('careers', 'frontend-intern.png'),
      job_description: '<h3>Role Summary</h3><p>Support the implementation of responsive Vue/Nuxt interfaces for company profile websites, dashboards, and CMS-backed landing pages.</p><h3>Responsibilities</h3><ul><li>Build clean UI components from approved layouts.</li><li>Verify mobile responsiveness and public content rendering.</li><li>Work with backend API responses and loading states.</li></ul><h3>Requirements</h3><ul><li>Basic understanding of HTML, CSS, JavaScript, and Vue.</li><li>Careful attention to spacing, typography, and accessibility.</li></ul>',
    }),
    upsertBy(Career, { job_title: 'Backend Developer Intern' }, {
      thumbnail: copyAsset('careers', 'backend-intern.png'),
      job_description: '<h3>Role Summary</h3><p>Assist backend development for CRUD modules, API validation, upload handling, authentication, and database-backed dashboard features.</p><h3>Responsibilities</h3><ul><li>Create and maintain REST API endpoints.</li><li>Improve validation and error handling.</li><li>Support database seed and testing workflows.</li></ul><h3>Requirements</h3><ul><li>Basic Node.js and SQL knowledge.</li><li>Interest in clean API contracts and secure input handling.</li></ul>',
    }),
    upsertBy(Career, { job_title: 'UI/UX Designer Intern' }, {
      thumbnail: copyAsset('careers', 'uiux-intern.png'),
      job_description: '<h3>Role Summary</h3><p>Contribute to interface concepts for corporate websites, dashboards, admin tools, and mobile-friendly digital products.</p><h3>Responsibilities</h3><ul><li>Create wireframes and high-fidelity interface layouts.</li><li>Refine visual hierarchy, spacing, and content structure.</li><li>Collaborate with developers during implementation.</li></ul><h3>Requirements</h3><ul><li>Comfortable using Figma or similar tools.</li><li>Strong sense of typography and professional visual style.</li></ul>',
    }),
    upsertBy(Career, { job_title: 'Quality Assurance Intern' }, {
      thumbnail: copyAsset('careers', 'qa-intern.png'),
      job_description: '<h3>Role Summary</h3><p>Help test dashboard CRUD modules, public page synchronization, responsive layouts, upload behavior, and slug detail routes.</p><h3>Responsibilities</h3><ul><li>Prepare test cases and QA evidence.</li><li>Report bugs with clear reproduction steps.</li><li>Support Playwright automation for critical flows.</li></ul><h3>Requirements</h3><ul><li>Careful documentation habits.</li><li>Interest in frontend, backend, and user experience quality.</li></ul>',
    }),
    upsertBy(Career, { job_title: 'Full-stack Developer Intern' }, {
      thumbnail: copyAsset('careers', 'fullstack-intern.png'),
      job_description: '<h3>Role Summary</h3><p>Support full-stack delivery across frontend views, backend APIs, CMS modules, and public website sections.</p><h3>Responsibilities</h3><ul><li>Implement small features from frontend to backend.</li><li>Maintain clean code and clear API usage.</li><li>Assist with debugging and integration testing.</li></ul><h3>Requirements</h3><ul><li>Basic Vue or React knowledge.</li><li>Basic Node.js, SQL, and REST API understanding.</li></ul>',
    }),
  ]);

  await Promise.all([
    upsertBy(ContactMessage, { email: 'rani.pradipta@example.co.id', subject: 'Inquiry about Company Profile Website' }, {
      name: 'Rani Pradipta',
      message: 'Kami ingin mendiskusikan pengembangan company profile website dengan CMS untuk layanan, portfolio, berita, dan karier.',
      is_read: false,
    }),
    upsertBy(ContactMessage, { email: 'andi.saputra@example.co.id', subject: 'Request for CMS Development' }, {
      name: 'Andi Saputra',
      message: 'Perusahaan kami membutuhkan CMS internal untuk mengelola publikasi konten dan halaman landing page tanpa bergantung pada developer setiap waktu.',
      is_read: false,
    }),
    upsertBy(ContactMessage, { email: 'mira.hapsari@example.co.id', subject: 'Consultation for Dashboard System' }, {
      name: 'Mira Hapsari',
      message: 'Kami ingin konsultasi pembuatan dashboard operasional yang menampilkan data ringkas, filter, dan laporan performa harian.',
      is_read: true,
    }),
    upsertBy(ContactMessage, { email: 'yusuf.firmansyah@example.co.id', subject: 'Question about QR Attendance System' }, {
      name: 'Yusuf Firmansyah',
      message: 'Apakah sistem QR attendance dapat disesuaikan untuk beberapa lokasi kantor dan export laporan bulanan?',
      is_read: false,
    }),
    upsertBy(ContactMessage, { email: 'dewi.lestari@example.co.id', subject: 'Partnership Opportunity' }, {
      name: 'Dewi Lestari',
      message: 'Kami tertarik menjajaki partnership untuk proyek website, mobile app, dan integrasi API pada beberapa client kami.',
      is_read: true,
    }),
  ]);

  await upsertBy(CompanyProfile, { id: 1 }, {
    company_name: 'Havor Smarta Digital',
    tagline: 'Your Digital IT Partner Solution',
    short_description: 'Integrated technology partner for websites, custom applications, mobile apps, enterprise systems, dashboards, and intelligent digital solutions.',
    long_description: 'Havor Smarta Digital is an Information Technology company specializing in digital solutions and application development. Since its early project journey in 2010 and official establishment as an IT startup in 2019, Havor has delivered scalable technology solutions for businesses across various industries.',
    email: 'bisnis@havorsmartadigital.com',
    phone: '+62-813-8036-2223 / +62-815-8690-2223',
    website: 'https://www.havorsmartadigital.com',
    address: 'Rukan Andalan, Jl. Asem Baris Raya No 15C, Tebet Jakarta Selatan',
    linkedin_url: 'https://www.linkedin.com/company/havor-smarta-digital',
    instagram_url: 'https://www.instagram.com/havorsmartadigital',
    logo_url: copyAsset('profile', 'havor-profile-logo.png') || '/uploads/profile/havor-profile-logo.png',
    seo_title: 'Havor Smarta Digital - Your Digital IT Partner Solution',
    seo_description: 'Havor Smarta Digital supports business growth through custom applications, websites, mobile apps, enterprise IT solutions, dashboards, and intelligent digital systems.',
  });

  console.log('Realistic Havor sample data has been created or updated successfully.');
  await sequelize.close();
};

seed().catch(async (error) => {
  console.error(error);
  await sequelize.close();
  process.exit(1);
});
