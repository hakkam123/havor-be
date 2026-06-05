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
const Campaign = require('./models/Campaign');
const Career = require('./models/Career');
const CareerApplication = require('./models/CareerApplication');
const ContactMessage = require('./models/ContactMessage');
const CompanyProfile = require('./models/CompanyProfile');

require('dotenv').config();

const projectRoot = path.join(__dirname, '..');
const uploadRoot = path.join(projectRoot, 'uploads');
const seedAssetRoot = path.join(projectRoot, 'seed-assets');
const frontendPublicRoot = path.resolve(
  process.env.SEED_FRONTEND_PUBLIC_ROOT || path.join(projectRoot, '..', 'havor-frontend', 'public')
);
const frontendBannerRoot = path.join(frontendPublicRoot, 'images', 'banner');

const bannerImages = [
  '001.jpg',
  '002.jpg',
  '003.jpg',
  '004.jpg',
  '005.jpg',
  '006.jpg',
  '007.jpg',
  '012.jpg',
  '013.jpg',
  '016.jpg',
  '018.jpg',
  '019.jpg',
  '020.jpg',
  '022.jpg',
  '023.jpg',
  '024.jpg',
  '027.jpg',
  '028.jpg',
  '034.jpg',
];

const remoteImages = [
  'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1600&q=80',
  'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1600&q=80',
  'https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=1600&q=80',
  'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1600&q=80',
  'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1600&q=80',
  'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1600&q=80',
  'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1600&q=80',
  'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1600&q=80',
  'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1600&q=80',
  'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1600&q=80',
];

const toSlug = (value) => slugify(value, { lower: true, strict: true });

const remoteImageFor = (index) => remoteImages[index % remoteImages.length];

const ensureFolder = (folder) => {
  const targetFolder = path.join(uploadRoot, folder);
  fs.mkdirSync(targetFolder, { recursive: true });
  return targetFolder;
};

const sourceAssetPath = (folder, fileName) => {
  const localSeedAsset = path.join(seedAssetRoot, folder, fileName);
  const frontendAsset = path.join(frontendBannerRoot, fileName);

  if (fs.existsSync(localSeedAsset)) return localSeedAsset;
  if (fs.existsSync(frontendAsset)) return frontendAsset;

  return null;
};

const copyAsset = (folder, fileName, outputName = fileName, fallbackIndex = 0) => {
  const sourcePath = sourceAssetPath(folder, fileName);

  if (!sourcePath) {
    return remoteImageFor(fallbackIndex);
  }

  const targetFolder = ensureFolder(folder);
  const safeName = `havor-${folder}-${outputName}`;
  const targetPath = path.join(targetFolder, safeName);

  fs.copyFileSync(sourcePath, targetPath);

  return `/uploads/${folder}/${safeName}`;
};

const copyPublicAsset = (
  folder,
  relativePath,
  outputName = path.basename(relativePath),
  fallbackIndex = 0
) => {
  const sourcePath = path.join(frontendPublicRoot, relativePath);

  if (!fs.existsSync(sourcePath)) {
    return remoteImageFor(fallbackIndex);
  }

  const targetFolder = ensureFolder(folder);
  const safeName = `havor-${folder}-${outputName}`;
  const targetPath = path.join(targetFolder, safeName);

  fs.copyFileSync(sourcePath, targetPath);

  return `/uploads/${folder}/${safeName}`;
};

const imageFor = (folder, index, outputName) => {
  const fileName = bannerImages[index % bannerImages.length];
  return copyAsset(folder, fileName, outputName || fileName, index);
};

const figure = (src, caption) => `
  <figure class="image">
    <img src="${src}" alt="${caption}">
    <figcaption>${caption}</figcaption>
  </figure>
`;

const listItems = (items) => items.map((item) => `<li>${item}</li>`).join('');

const richText = ({ title, intro, image, caption, sections, outcomes }) => `
  <h2>${title}</h2>
  <p>${intro}</p>
  ${image ? figure(image, caption || title) : ''}
  ${sections
    .map((section) => `
      <h3>${section.heading}</h3>
      <p>${section.body}</p>
      ${section.points ? `<ul>${listItems(section.points)}</ul>` : ''}
    `)
    .join('')}
  ${outcomes ? `<h3>Expected Outcome</h3><ul>${listItems(outcomes)}</ul>` : ''}
`;

const upsertBy = async (Model, where, values) => {
  const existing = await Model.findOne({ where });

  if (existing) {
    await existing.update({ ...values, ...where });
    return existing;
  }

  return Model.create({ ...values, ...where });
};

const productCategories = [
  'Company Profile Website',
  'CMS & Admin Dashboard',
  'Web Application',
  'Reporting Dashboard',
  'API Integration',
  'Digital Product',
];

const newsCategories = [
  'Company Update',
  'Engineering',
  'Product Insight',
  'Digital Transformation',
  'Security & Operations',
];

const campaignCategories = [
  'Digital Readiness',
  'CMS Modernization',
  'Operational Dashboard',
  'Long-Term Support',
  'API Integration',
];

const careerCategories = [
  'Engineering',
  'Design',
  'Quality Assurance',
  'Internship',
  'Business & Operations',
];

const categorySeeds = [
  ...productCategories.map((name) => ({ name, type: 'Product' })),
  ...newsCategories.map((name) => ({ name, type: 'News' })),
  ...campaignCategories.map((name) => ({ name, type: 'Campaign' })),
  ...careerCategories.map((name) => ({ name, type: 'Career' })),
];

const getCategoryId = (categoryMap, type, name) => {
  const category = categoryMap.get(`${type}:${name}`);
  return category ? category.id : null;
};

const seed = async () => {
  try {
    await connectDB();
    await sequelize.sync();

    const logoUrl = copyPublicAsset('company', 'logo-havor.svg', 'logo-havor.svg');

    await upsertBy(
      CompanyProfile,
      { company_name: 'PT Havor SMART Digital' },
      {
        tagline: 'Your Digital IT Partner Solution',
        short_description:
          'PT Havor SMART Digital helps organizations build reliable websites, CMS platforms, dashboards, and internal applications for day-to-day digital operations.',
        long_description: richText({
          title: 'About PT Havor SMART Digital',
          intro:
            'PT Havor SMART Digital is a digital technology partner focused on practical software delivery for businesses that need a cleaner, faster, and more maintainable operational workflow.',
          image: logoUrl,
          caption: 'PT Havor SMART Digital company identity',
          sections: [
            {
              heading: 'What We Build',
              body:
                'The team delivers company profile websites, content management systems, product catalog platforms, campaign pages, media publishing workflows, career portals, and management dashboards. The goal is not only to launch a website, but to give the client a system that can keep growing after launch.',
            },
            {
              heading: 'How We Work',
              body:
                'Every project starts from business context, content structure, data model, and daily admin workflow. This keeps the technical implementation simple enough to maintain while still covering the real needs of marketing, operations, recruitment, and management teams.',
            },
          ],
          outcomes: [
            'A structured digital platform with CMS-managed content.',
            'Reliable public pages for product, campaign, news, career, and portfolio content.',
            'Operational workflows that are easier to update without developer involvement.',
          ],
        }),
        email: 'hello@havorsmartadigital.com',
        phone: '+62 812-0000-0000',
        website: 'https://havorsmarta.vercel.app',
        address: 'Indonesia',
        linkedin_url: 'https://www.linkedin.com/company/havor-smart-digital',
        instagram_url: 'https://www.instagram.com/havorsmartadigital',
        logo_url: logoUrl,
        seo_title: 'PT Havor SMART Digital - Your Digital IT Partner Solution',
        seo_description:
          'PT Havor SMART Digital builds CMS-driven websites, dashboards, product catalogs, campaign pages, career portals, and internal digital tools.',
      }
    );

    await Promise.all(
      categorySeeds.map((category) =>
        upsertBy(Category, { name: category.name, type: category.type }, {})
      )
    );

    const categories = await Category.findAll();
    const categoryMap = new Map(categories.map((category) => [`${category.type}:${category.name}`, category]));

    const banners = [
      {
        page_name: 'home',
        title: 'Your Digital IT Partner Solution',
        subtitle:
          'Build reliable websites, CMS platforms, and business applications that are easy to operate after launch.',
        media_url: imageFor('banners', 1, 'home.jpg'),
      },
      {
        page_name: 'about-us',
        title: 'A Practical Technology Partner',
        subtitle:
          'We combine product thinking, clean engineering, and CMS-first delivery for businesses that need reliable digital operations.',
        media_url: imageFor('banners', 2, 'about-us.jpg'),
      },
      {
        page_name: 'services',
        title: 'Services Built Around Real Business Workflows',
        subtitle:
          'From company profiles to internal dashboards, every service is designed to be maintainable, useful, and CMS-ready.',
        media_url: imageFor('banners', 3, 'services.jpg'),
      },
      {
        page_name: 'products',
        title: 'Digital Products and Web Platforms',
        subtitle:
          'Explore CMS-managed product catalogs, dashboards, portals, and integrations designed for daily operational use.',
        media_url: imageFor('banners', 4, 'products.jpg'),
      },
      {
        page_name: 'product',
        title: 'Digital Product Catalog',
        subtitle:
          'Product pages are structured so every solution can be maintained from the CMS and published with consistent presentation.',
        media_url: imageFor('banners', 5, 'product.jpg'),
      },
      {
        page_name: 'projects',
        title: 'Selected Works',
        subtitle:
          'A collection of practical web, CMS, dashboard, and publishing workflows built for real operational needs.',
        media_url: imageFor('banners', 6, 'projects.jpg'),
      },
      {
        page_name: 'media-news',
        title: 'Media and News',
        subtitle:
          'Updates, engineering notes, product insights, and operational stories from the Havor SMART Digital team.',
        media_url: imageFor('banners', 7, 'media-news.jpg'),
      },
      {
        page_name: 'news',
        title: 'Company News and Insights',
        subtitle:
          'CMS-managed articles for company updates, engineering practices, product insights, and security operations.',
        media_url: imageFor('banners', 8, 'news.jpg'),
      },
      {
        page_name: 'careers',
        title: 'Careers at Havor SMART Digital',
        subtitle:
          'Join a team that values clean execution, practical product thinking, and technology that serves real users.',
        media_url: imageFor('banners', 9, 'careers.jpg'),
      },
      {
        page_name: 'contact',
        title: 'Start a Digital Project Conversation',
        subtitle:
          'Tell us about your website, CMS, dashboard, integration, or digital product needs.',
        media_url: imageFor('banners', 10, 'contact.jpg'),
      },
    ];

    await Promise.all(
      banners.map((banner) =>
        upsertBy(
          HeroBanner,
          { page_name: banner.page_name },
          {
            title: banner.title,
            subtitle: banner.subtitle,
            media_url: banner.media_url,
            media_type: 'image',
          }
        )
      )
    );

    const clients = [
      {
        name: 'PT Havor SMART Digital',
        description: 'Company-owned digital platform and internal product ecosystem.',
        client_icon: imageFor('clients', 11, 'pt-havor-smart-digital.jpg'),
      },
      {
        name: 'Havor Product Team',
        description: 'Internal product planning team for CMS, catalog, and dashboard workflows.',
        client_icon: imageFor('clients', 12, 'havor-product-team.jpg'),
      },
      {
        name: 'Havor Engineering Team',
        description: 'Engineering team responsible for frontend, backend, API, and deployment quality.',
        client_icon: imageFor('clients', 13, 'havor-engineering-team.jpg'),
      },
      {
        name: 'Havor Content Operations',
        description: 'Content operations workflow for news, campaign, product, and career publication.',
        client_icon: imageFor('clients', 14, 'havor-content-operations.jpg'),
      },
    ];

    await Promise.all(
      clients.map((client) => upsertBy(Client, { name: client.name }, client))
    );

    const serviceImages = {
      companyProfile: imageFor('expertises', 15, 'company-profile-website.jpg'),
      cms: imageFor('expertises', 16, 'cms-admin-dashboard.jpg'),
      application: imageFor('expertises', 17, 'web-application.jpg'),
      dashboard: imageFor('expertises', 18, 'reporting-dashboard.jpg'),
      integration: imageFor('expertises', 19, 'api-integration.jpg'),
      support: imageFor('expertises', 20, 'maintenance-support.jpg'),
    };

    const services = [
      {
        name: 'Company Profile Website Development',
        description: richText({
          title: 'Company Profile Website Development',
          intro:
            'A company profile website should explain the business clearly, load quickly, and stay easy to update. Havor SMART Digital builds company websites with structured pages, CMS-managed content, SEO-ready metadata, and visual sections that help visitors understand the company without friction.',
          image: serviceImages.companyProfile,
          caption: 'Company profile website managed from CMS',
          sections: [
            {
              heading: 'Scope of Work',
              body:
                'The work includes information architecture, public page implementation, responsive layout, CMS fields, banner management, service pages, portfolio pages, and contact entry points. The CMS is kept straightforward so non-technical users can update copy, images, and content categories.',
              points: [
                'Landing page, about page, services, portfolio, media, careers, and contact sections.',
                'CMS-managed hero banners, company profile, service descriptions, and portfolio content.',
                'SEO title and description fields for production publishing.',
              ],
            },
            {
              heading: 'Production Consideration',
              body:
                'The implementation avoids unnecessary animation and fragile content layouts. Images, headings, descriptions, and CTA areas are designed to remain stable when the admin changes content from the dashboard.',
            },
          ],
          outcomes: [
            'Professional public website that can be updated from CMS.',
            'Clear company narrative for visitors and prospective clients.',
            'Lower dependency on developers for routine content updates.',
          ],
        }),
        icon_url: serviceImages.companyProfile,
      },
      {
        name: 'CMS and Admin Dashboard Development',
        description: richText({
          title: 'CMS and Admin Dashboard Development',
          intro:
            'A useful CMS is not only a form collection. It should match how the business publishes product, news, campaign, career, inbox, banner, client, and profile data. Havor SMART Digital builds admin dashboards with clean content workflows and predictable data structure.',
          image: serviceImages.cms,
          caption: 'CMS dashboard for daily content operations',
          sections: [
            {
              heading: 'Content Workflow',
              body:
                'The CMS separates taxonomy, publishing status, rich text, media upload, and operational inboxes so each admin menu has a clear responsibility. TinyMCE content is supported for long descriptions and article pages, including inline images when the content needs visual context.',
              points: [
                'Category management for Product, News, Campaign, and Career content.',
                'Admin forms for banners, services, products, works, news, campaigns, careers, clients, and company profile.',
                'Readable filtering for status and category-based content discovery.',
              ],
            },
            {
              heading: 'Maintainability',
              body:
                'The dashboard is designed around simple models and direct API contracts. That keeps future changes easier, especially when new public pages need to fetch the same CMS content.',
            },
          ],
          outcomes: [
            'One dashboard for all public website content.',
            'Structured data that frontend pages can fetch consistently.',
            'Long-form content support through rich text fields.',
          ],
        }),
        icon_url: serviceImages.cms,
      },
      {
        name: 'Custom Web Application Development',
        description: richText({
          title: 'Custom Web Application Development',
          intro:
            'Internal web applications help teams replace scattered spreadsheets and manual follow-ups with a clearer workflow. Havor SMART Digital builds practical web apps for forms, approvals, tracking, reporting, and team operations.',
          image: serviceImages.application,
          caption: 'Custom web application for operational workflows',
          sections: [
            {
              heading: 'Application Focus',
              body:
                'The development starts from workflow mapping: who creates the data, who reviews it, what must be shown publicly, and what should stay private. The result is a web application that supports actual daily work instead of adding another complicated system.',
              points: [
                'Role-aware admin pages and public submission forms.',
                'API-driven frontend and backend separation.',
                'Validation and error handling at the correct application layer.',
              ],
            },
            {
              heading: 'Delivery Standard',
              body:
                'The codebase is kept simple, readable, and aligned with the existing stack. Features are delivered around current business needs instead of speculative modules that create maintenance cost.',
            },
          ],
          outcomes: [
            'Cleaner operational process.',
            'Reusable API and frontend structure.',
            'A maintainable base for future feature expansion.',
          ],
        }),
        icon_url: serviceImages.application,
      },
      {
        name: 'Operational Dashboard and Reporting',
        description: richText({
          title: 'Operational Dashboard and Reporting',
          intro:
            'Dashboards are valuable when they help teams make faster decisions from real data. Havor SMART Digital designs dashboard views for content performance, inquiries, applications, publishing status, and internal operational metrics.',
          image: serviceImages.dashboard,
          caption: 'Operational dashboard for management visibility',
          sections: [
            {
              heading: 'Dashboard Structure',
              body:
                'The dashboard organizes metrics into useful groups instead of noisy charts. Status summaries, category totals, latest activity, and operational queues are prioritized because they directly support the admin team.',
              points: [
                'Content totals and publishing status.',
                'Inbox and career application visibility.',
                'Category-level summaries for product, news, campaign, and career data.',
              ],
            },
            {
              heading: 'Data Quality',
              body:
                'Dashboard data is only helpful when the source models are clear. The implementation keeps category types explicit and avoids mixing unrelated content in the same filter.',
            },
          ],
          outcomes: [
            'Better visibility for content and operations.',
            'Faster review of pending records.',
            'Cleaner reporting structure for decision makers.',
          ],
        }),
        icon_url: serviceImages.dashboard,
      },
      {
        name: 'API Integration and Backend Services',
        description: richText({
          title: 'API Integration and Backend Services',
          intro:
            'Modern websites often need to connect public pages, CMS dashboards, storage, email notifications, and third-party services. Havor SMART Digital builds backend APIs that keep those integrations clear and secure.',
          image: serviceImages.integration,
          caption: 'Backend API integration for digital platforms',
          sections: [
            {
              heading: 'Integration Areas',
              body:
                'The backend service can handle authentication, content publishing, file upload, email delivery, career application submission, contact messages, and structured public data endpoints. Each endpoint is kept focused so frontend pages can consume the data safely.',
              points: [
                'REST API for CMS-managed content.',
                'File upload and media URL management.',
                'Email and notification workflow for operational messages.',
              ],
            },
            {
              heading: 'Security Consideration',
              body:
                'Validation is applied at the API boundary, sensitive errors are not exposed to users, and operational data is separated from public content endpoints.',
            },
          ],
          outcomes: [
            'Stable API contract for frontend and CMS.',
            'Safer handling of files, messages, and applications.',
            'Integration-ready backend foundation.',
          ],
        }),
        icon_url: serviceImages.integration,
      },
      {
        name: 'Website Maintenance and Production Support',
        description: richText({
          title: 'Website Maintenance and Production Support',
          intro:
            'After launch, a digital platform needs monitoring, small improvements, content support, bug fixes, and deployment discipline. Havor SMART Digital supports production systems so the website stays useful beyond the first release.',
          image: serviceImages.support,
          caption: 'Production support for digital operations',
          sections: [
            {
              heading: 'Support Coverage',
              body:
                'Maintenance includes checking public pages, admin workflows, image delivery, data consistency, content publishing, and application forms. Small improvements are prioritized based on operational impact.',
              points: [
                'Bug fixing and deployment support.',
                'CMS workflow adjustment.',
                'Content structure and media review.',
              ],
            },
            {
              heading: 'Long-Term Value',
              body:
                'The goal is to keep the platform clean, understandable, and useful. Maintenance avoids stacking quick hacks that make future changes harder.',
            },
          ],
          outcomes: [
            'More stable production website.',
            'Less friction for admin users.',
            'A healthier codebase over time.',
          ],
        }),
        icon_url: serviceImages.support,
      },
    ];

    await Promise.all(
      services.map((service) => upsertBy(Expertise, { name: service.name }, service))
    );

    const products = [
      {
        name: 'CMS-Driven Company Profile Platform',
        category: 'Company Profile Website',
        image_url: imageFor('products', 21, 'cms-driven-company-profile.jpg'),
        external_link: 'https://havorsmarta.vercel.app',
        description: richText({
          title: 'CMS-Driven Company Profile Platform',
          intro:
            'This product is a production-ready company profile platform for organizations that need a public website with manageable content. The platform includes structured hero banners, company profile data, service pages, product catalogs, works, news, campaigns, career pages, and contact forms.',
          image: imageFor('products', 22, 'cms-driven-company-profile-inline.jpg'),
          caption: 'Company profile platform with CMS-managed sections',
          sections: [
            {
              heading: 'Core Capability',
              body:
                'The website is built around CMS data instead of hardcoded page content. This gives the admin team control over important public sections while keeping the frontend layout consistent and professional.',
              points: [
                'Hero banner and company profile management.',
                'Dynamic service, product, work, news, campaign, and career content.',
                'Responsive public pages with stable visual layout.',
              ],
            },
            {
              heading: 'Ideal Use Case',
              body:
                'The platform fits companies that need to publish trustworthy business information, update service offerings, display selected works, and manage hiring or campaign pages from one dashboard.',
            },
          ],
          outcomes: [
            'Faster content updates.',
            'Cleaner public company presentation.',
            'Lower maintenance cost for routine website changes.',
          ],
        }),
      },
      {
        name: 'Operational CMS and Admin Dashboard',
        category: 'CMS & Admin Dashboard',
        image_url: imageFor('products', 23, 'operational-cms-dashboard.jpg'),
        external_link: 'https://havorsmarta.vercel.app/admin',
        description: richText({
          title: 'Operational CMS and Admin Dashboard',
          intro:
            'This dashboard product gives content and operations teams a focused interface for managing public website records. It covers taxonomy, content publishing, rich text editing, media fields, and operational inboxes.',
          image: imageFor('products', 24, 'operational-cms-dashboard-inline.jpg'),
          caption: 'Admin dashboard for CMS operations',
          sections: [
            {
              heading: 'Managed Menus',
              body:
                'The dashboard supports categories, hero banners, company profile, services, clients, works, products, news, campaigns, careers, contact messages, and career applications. Each menu maps directly to the public frontend or operational workflow.',
              points: [
                'Category types for Product, News, Campaign, and Career.',
                'TinyMCE-based rich text content for long descriptions and articles.',
                'Status and category filters for easier admin review.',
              ],
            },
            {
              heading: 'Operational Benefit',
              body:
                'Admin users can update content without waiting for code changes, while developers keep the data model explicit and predictable.',
            },
          ],
          outcomes: [
            'Centralized CMS operations.',
            'Consistent API data for frontend pages.',
            'Reduced manual deployment for content changes.',
          ],
        }),
      },
      {
        name: 'Product Catalog and Solution Showcase',
        category: 'Digital Product',
        image_url: imageFor('products', 25, 'product-catalog-showcase.jpg'),
        external_link: 'https://havorsmarta.vercel.app/products',
        description: richText({
          title: 'Product Catalog and Solution Showcase',
          intro:
            'This product helps technology and service companies present solution offerings in a structured catalog. Each product can be assigned to a category, supported with image assets, and expanded with rich text content.',
          image: imageFor('products', 26, 'product-catalog-showcase-inline.jpg'),
          caption: 'Structured product catalog managed from CMS',
          sections: [
            {
              heading: 'Catalog Structure',
              body:
                'Product categories make it easier for visitors to browse solution groups. Admin users can maintain product copy, feature highlights, case context, and external links from the dashboard.',
              points: [
                'Category-based product filtering.',
                'Rich description support with inline media.',
                'External links for demo, proposal, or documentation pages.',
              ],
            },
            {
              heading: 'Frontend Integration',
              body:
                'The public product page fetches live CMS data, so categories and product cards remain aligned with what admins manage.',
            },
          ],
          outcomes: [
            'Better solution discovery.',
            'Reusable product page pattern.',
            'CMS-controlled product messaging.',
          ],
        }),
      },
      {
        name: 'Management Reporting Dashboard',
        category: 'Reporting Dashboard',
        image_url: imageFor('products', 27, 'management-reporting-dashboard.jpg'),
        external_link: 'https://havorsmarta.vercel.app/projects',
        description: richText({
          title: 'Management Reporting Dashboard',
          intro:
            'This dashboard product helps management teams review website content, inquiries, applications, and publishing activity from structured data. The focus is clarity, not decorative charts.',
          image: imageFor('products', 28, 'management-reporting-dashboard-inline.jpg'),
          caption: 'Reporting dashboard for management visibility',
          sections: [
            {
              heading: 'Reporting Scope',
              body:
                'The dashboard can summarize total content records, latest submissions, category distribution, and operational queues. These views help teams know what needs review and what has already been published.',
              points: [
                'Content totals by CMS menu.',
                'Latest contact messages and career applications.',
                'Category and status distribution.',
              ],
            },
            {
              heading: 'Decision Support',
              body:
                'The product is designed for daily visibility so managers can spot gaps in content, campaign activity, or recruitment response without opening every record manually.',
            },
          ],
          outcomes: [
            'Faster operational review.',
            'Clearer management visibility.',
            'Less manual reporting work.',
          ],
        }),
      },
      {
        name: 'API Integration Layer for Digital Platforms',
        category: 'API Integration',
        image_url: imageFor('products', 29, 'api-integration-layer.jpg'),
        external_link: 'https://havorsmarta.vercel.app/services',
        description: richText({
          title: 'API Integration Layer for Digital Platforms',
          intro:
            'This backend product connects public pages, admin dashboards, media uploads, email notifications, and operational data. It is built for teams that need a dependable API layer without unnecessary complexity.',
          image: imageFor('products', 30, 'api-integration-layer-inline.jpg'),
          caption: 'API integration layer for CMS and public website data',
          sections: [
            {
              heading: 'Integration Capability',
              body:
                'The API provides endpoints for content records, form submissions, career applications, and dashboard data. It keeps public data retrieval separate from protected admin operations.',
              points: [
                'REST endpoints for CMS content.',
                'Upload handling for images and career CV files.',
                'Email-ready workflow for contact and recruitment actions.',
              ],
            },
            {
              heading: 'Production Reliability',
              body:
                'The implementation is kept direct and readable, with validation at request boundaries and controlled error responses for users.',
            },
          ],
          outcomes: [
            'Cleaner frontend and backend contract.',
            'Safer operational data handling.',
            'Integration-ready platform foundation.',
          ],
        }),
      },
    ];

    await Promise.all(
      products.map((product) =>
        upsertBy(
          Product,
          { name: product.name },
          {
            description: product.description,
            image_url: product.image_url,
            external_link: product.external_link,
            categoryId: getCategoryId(categoryMap, 'Product', product.category),
          }
        )
      )
    );

    const works = [
      {
        title: 'Havor SMART Digital Company Website',
        client: 'PT Havor SMART Digital',
        year: 2026,
        category: 'Company Profile Website',
        image_url: imageFor('works', 31, 'havor-company-website.jpg'),
        description:
          'CMS-managed company profile website with dynamic banner, service, product, work, news, campaign, career, and contact content.',
      },
      {
        title: 'CMS Taxonomy and Publishing Workflow',
        client: 'Havor Content Operations',
        year: 2026,
        category: 'CMS & Admin Dashboard',
        image_url: imageFor('works', 32, 'cms-taxonomy-publishing-workflow.jpg'),
        description:
          'Admin workflow for managing Product, News, Campaign, and Career taxonomy so frontend pages can fetch categories directly from CMS data.',
      },
      {
        title: 'Media, News, and Campaign Publishing System',
        client: 'Havor Product Team',
        year: 2026,
        category: 'Web Application',
        image_url: imageFor('works', 33, 'media-news-campaign-system.jpg'),
        description:
          'Publishing system for long-form TinyMCE content, article thumbnails, campaign pages, and category-based public filtering.',
      },
      {
        title: 'Career Portal and Application Intake',
        client: 'Havor Business Operations',
        year: 2026,
        category: 'Web Application',
        image_url: imageFor('works', 34, 'career-portal-application-intake.jpg'),
        description:
          'Career page and application intake workflow for collecting applicant data, portfolio links, experience summaries, and CV metadata.',
      },
      {
        title: 'Operational Content Dashboard',
        client: 'Havor Engineering Team',
        year: 2026,
        category: 'Reporting Dashboard',
        image_url: imageFor('works', 35, 'operational-content-dashboard.jpg'),
        description:
          'Dashboard concept for tracking CMS record totals, latest inbox activity, category health, and publishing operations.',
      },
    ];

    await Promise.all(
      works.map((work) =>
        upsertBy(
          Work,
          { title: work.title },
          {
            description: work.description,
            image_url: work.image_url,
            client: work.client,
            year: work.year,
            categoryId: getCategoryId(categoryMap, 'Product', work.category),
          }
        )
      )
    );

    const newsItems = [
      {
        title: 'Havor SMART Digital Strengthens CMS-Based Website Delivery',
        category: 'Company Update',
        image_url: imageFor('news', 36, 'cms-based-website-delivery.jpg'),
        content: richText({
          title: 'Havor SMART Digital Strengthens CMS-Based Website Delivery',
          intro:
            'Havor SMART Digital continues to strengthen its CMS-based website delivery model for companies that need public websites with practical content operations. The approach focuses on giving business teams direct control over daily website updates while keeping the engineering foundation clean and maintainable.',
          image: imageFor('news', 37, 'cms-based-website-delivery-inline.jpg'),
          caption: 'CMS-based website delivery for practical content operations',
          sections: [
            {
              heading: 'Why CMS Delivery Matters',
              body:
                'Many company websites become outdated because every small content update depends on developer availability. A CMS-driven structure reduces that bottleneck by separating content management from code deployment. Admin teams can update banners, company profile details, services, products, works, articles, campaigns, and career posts through a controlled dashboard.',
            },
            {
              heading: 'Operational Impact',
              body:
                'The operational value appears after launch. Marketing can publish product updates, HR can post career openings, and management can review incoming messages without waiting for a full release cycle. This creates a website that keeps moving with the business.',
              points: [
                'Faster content update cycle.',
                'Clearer ownership between admin users and developers.',
                'More consistent public presentation across pages.',
              ],
            },
            {
              heading: 'Engineering Standard',
              body:
                'The technical direction stays pragmatic. The backend exposes focused endpoints, the frontend fetches structured CMS data, and rich text is handled where long-form content is needed. This avoids overbuilding while keeping enough flexibility for real production needs.',
            },
          ],
          outcomes: [
            'A stronger CMS foundation for future company websites.',
            'Better content ownership for business teams.',
            'More maintainable frontend and backend integration.',
          ],
        }),
      },
      {
        title: 'Designing Category Types for Product, News, Campaign, and Career Content',
        category: 'Engineering',
        image_url: imageFor('news', 38, 'category-types-engineering.jpg'),
        content: richText({
          title: 'Designing Category Types for Product, News, Campaign, and Career Content',
          intro:
            'A clean taxonomy model is one of the quiet foundations of a usable CMS. Havor SMART Digital uses explicit category types for Product, News, Campaign, and Career content so each public page can fetch the right list without mixing unrelated records.',
          image: imageFor('news', 39, 'category-types-engineering-inline.jpg'),
          caption: 'Explicit CMS category types keep public pages predictable',
          sections: [
            {
              heading: 'The Problem With Generic Categories',
              body:
                'When every category sits in one untyped list, admin users may accidentally reuse a product category for an article or a news category for a campaign. The frontend then needs extra assumptions to decide what belongs on each page. That creates hidden complexity.',
            },
            {
              heading: 'Typed Taxonomy',
              body:
                'Typed taxonomy keeps the model direct. Product pages fetch Product categories, media pages fetch News categories, campaign pages fetch Campaign categories, and career pages fetch Career categories. The CMS remains simple while the frontend gets reliable data.',
              points: [
                'Product categories support catalogs and selected works.',
                'News categories support articles and company updates.',
                'Campaign categories support promotional or educational programs.',
                'Career categories support hiring content and recruitment grouping.',
              ],
            },
            {
              heading: 'Frontend Benefit',
              body:
                'Typed categories reduce defensive code on the frontend. Components can request the category type they need and render the result directly. This improves readability and keeps future menu additions easier to reason about.',
            },
          ],
          outcomes: [
            'Cleaner CMS taxonomy.',
            'Lower risk of mixed content on public pages.',
            'Simpler frontend data fetching.',
          ],
        }),
      },
      {
        title: 'Why Long-Form Rich Text Still Matters for Service and News Pages',
        category: 'Product Insight',
        image_url: imageFor('news', 40, 'rich-text-service-news.jpg'),
        content: richText({
          title: 'Why Long-Form Rich Text Still Matters for Service and News Pages',
          intro:
            'Short cards are useful for scanning, but they are not enough when a company needs to explain a service, publish a detailed update, or document a campaign. Long-form rich text gives editors room to communicate clearly without asking developers to redesign the page every time.',
          image: imageFor('news', 41, 'rich-text-service-news-inline.jpg'),
          caption: 'Long-form rich text with inline media for CMS content',
          sections: [
            {
              heading: 'Better Editorial Control',
              body:
                'TinyMCE-style rich text gives content teams the ability to write structured sections, add lists, insert images, and provide supporting context. This is important for service pages because visitors often need more than a headline before they trust the offering.',
            },
            {
              heading: 'Useful Visual Context',
              body:
                'Inline images can support the story when used carefully. A service article can show a workflow preview, a news update can include an event or product image, and a campaign page can explain program details with a clear visual break.',
              points: [
                'Use images to clarify the content, not to decorate every paragraph.',
                'Keep captions descriptive for accessibility and editorial clarity.',
                'Avoid oversized assets that slow down public pages.',
              ],
            },
            {
              heading: 'Clean Implementation',
              body:
                'The CMS stores rich text in content fields while the frontend renders it in a controlled content area. This keeps the page template stable and lets editors focus on writing.',
            },
          ],
          outcomes: [
            'More complete service explanations.',
            'Better public article quality.',
            'Less developer involvement for editorial updates.',
          ],
        }),
      },
      {
        title: 'Practical Security Notes for CMS-Managed Public Websites',
        category: 'Security & Operations',
        image_url: imageFor('news', 42, 'cms-security-operations.jpg'),
        content: richText({
          title: 'Practical Security Notes for CMS-Managed Public Websites',
          intro:
            'A CMS-managed website must balance editorial flexibility with safe operational boundaries. Havor SMART Digital applies practical security controls at the API, authentication, validation, and file handling layers.',
          image: imageFor('news', 43, 'cms-security-operations-inline.jpg'),
          caption: 'Security and operations for CMS-managed websites',
          sections: [
            {
              heading: 'API Boundary',
              body:
                'Validation belongs at the request boundary. Incoming form data, category types, file metadata, and content status should be checked before data reaches the application logic. This keeps internal functions simpler and reduces repeated defensive checks.',
            },
            {
              heading: 'Content and Upload Safety',
              body:
                'Rich text and uploaded files need clear rules. Admin-only editing, accepted file types, upload size limits, and controlled public URLs help reduce avoidable risks.',
              points: [
                'Validate request payloads before database writes.',
                'Limit file type and file size for uploads.',
                'Avoid exposing stack traces or sensitive operational details to public users.',
              ],
            },
            {
              heading: 'Operational Practice',
              body:
                'Security also depends on routine practice: rotating secrets when needed, protecting admin routes, reviewing production logs, and keeping dependencies updated. These habits matter as much as the first implementation.',
            },
          ],
          outcomes: [
            'Safer CMS operations.',
            'Cleaner API responsibilities.',
            'Reduced exposure of sensitive implementation details.',
          ],
        }),
      },
      {
        title: 'Digital Transformation Starts With Maintainable Everyday Tools',
        category: 'Digital Transformation',
        image_url: imageFor('news', 44, 'maintainable-everyday-tools.jpg'),
        content: richText({
          title: 'Digital Transformation Starts With Maintainable Everyday Tools',
          intro:
            'Digital transformation does not always start with a large enterprise system. For many organizations, the most valuable first step is a maintainable website, a clear dashboard, a reliable form workflow, or a CMS that reduces daily friction.',
          image: imageFor('news', 45, 'maintainable-everyday-tools-inline.jpg'),
          caption: 'Maintainable tools for everyday digital operations',
          sections: [
            {
              heading: 'Start From Current Workflows',
              body:
                'The best digital tool is built from how the team already works. Before writing code, it is important to understand what data is created, who reviews it, what becomes public, and what decisions depend on it.',
            },
            {
              heading: 'Avoid Unused Complexity',
              body:
                'A system full of speculative features becomes expensive to maintain. Havor SMART Digital prefers a clear first version that solves current work, then improves based on real usage.',
              points: [
                'Map the current operational problem.',
                'Build the smallest reliable workflow that solves it.',
                'Improve after real users have worked with the system.',
              ],
            },
            {
              heading: 'Long-Term Maintainability',
              body:
                'Maintainability is what lets a digital system keep serving the business. Clean models, readable code, predictable API contracts, and CMS-owned content make the platform easier to grow.',
            },
          ],
          outcomes: [
            'More useful first releases.',
            'Lower cost of future changes.',
            'Digital tools that match daily work.',
          ],
        }),
      },
    ];

    await Promise.all(
      newsItems.map((news) =>
        upsertBy(
          News,
          { slug: toSlug(news.title) },
          {
            title: news.title,
            slug: toSlug(news.title),
            content: news.content,
            image_url: news.image_url,
            category: news.category,
            is_published: true,
          }
        )
      )
    );

    const campaignItems = [
      {
        title: 'Digital Readiness Check for Company Websites',
        category: 'Digital Readiness',
        image_url: imageFor('campaigns', 46, 'digital-readiness-check.jpg'),
        content: richText({
          title: 'Digital Readiness Check for Company Websites',
          intro:
            'This campaign helps organizations review whether their current website is ready to support content updates, lead capture, product communication, career publication, and long-term maintenance.',
          image: imageFor('campaigns', 47, 'digital-readiness-check-inline.jpg'),
          caption: 'Digital readiness review for company websites',
          sections: [
            {
              heading: 'Campaign Focus',
              body:
                'The review covers public page structure, content ownership, CMS availability, media quality, SEO basics, contact workflow, and admin handover readiness. The purpose is to identify practical gaps before they become expensive production problems.',
              points: [
                'Website structure and navigation clarity.',
                'CMS readiness for content updates.',
                'Contact, career, and inquiry workflow review.',
              ],
            },
            {
              heading: 'Who Should Join',
              body:
                'This campaign is suitable for companies preparing a website redesign, teams that still rely on developers for every content change, and organizations that want a cleaner CMS-managed public presence.',
            },
          ],
          outcomes: [
            'A practical website readiness checklist.',
            'Clear improvement priorities.',
            'Better preparation for CMS-based website delivery.',
          ],
        }),
      },
      {
        title: 'CMS Modernization Program for Growing Businesses',
        category: 'CMS Modernization',
        image_url: imageFor('campaigns', 48, 'cms-modernization-program.jpg'),
        content: richText({
          title: 'CMS Modernization Program for Growing Businesses',
          intro:
            'The CMS Modernization Program is designed for businesses whose website content has outgrown hardcoded pages or scattered update requests. The program moves important website sections into a cleaner admin workflow.',
          image: imageFor('campaigns', 49, 'cms-modernization-program-inline.jpg'),
          caption: 'CMS modernization for scalable content operations',
          sections: [
            {
              heading: 'Modernization Scope',
              body:
                'The program focuses on moving repeatable content into structured CMS records: banners, services, products, works, news, campaigns, careers, clients, and company profile details. This allows business users to update the website without touching the codebase.',
              points: [
                'Audit current content sections.',
                'Define CMS models and category types.',
                'Integrate public frontend pages with CMS APIs.',
              ],
            },
            {
              heading: 'Expected Change',
              body:
                'After modernization, the website becomes easier to operate. Developers can focus on improving the platform while admins manage day-to-day content.',
            },
          ],
          outcomes: [
            'CMS-controlled public content.',
            'Reduced hardcoded page updates.',
            'Cleaner handover for admin users.',
          ],
        }),
      },
      {
        title: 'Operational Dashboard Starter Campaign',
        category: 'Operational Dashboard',
        image_url: imageFor('campaigns', 50, 'operational-dashboard-starter.jpg'),
        content: richText({
          title: 'Operational Dashboard Starter Campaign',
          intro:
            'This campaign introduces a practical dashboard approach for teams that need visibility into content, inquiries, applications, and publishing status without building a large reporting system first.',
          image: imageFor('campaigns', 51, 'operational-dashboard-starter-inline.jpg'),
          caption: 'Starter dashboard for operational visibility',
          sections: [
            {
              heading: 'Dashboard Foundation',
              body:
                'The starter dashboard emphasizes high-signal metrics: total records, latest submissions, category distribution, unpublished content, and operational queues. It avoids complex visualizations that do not help daily decision making.',
              points: [
                'Content totals by CMS module.',
                'Latest contact and recruitment activity.',
                'Publishing status and category health.',
              ],
            },
            {
              heading: 'Business Value',
              body:
                'Teams get a shared view of what is happening in the CMS and can respond faster to pending operational records.',
            },
          ],
          outcomes: [
            'Faster admin review.',
            'Improved operational awareness.',
            'A dashboard foundation that can grow from real usage.',
          ],
        }),
      },
      {
        title: 'Long-Term Website Support and Maintenance Package',
        category: 'Long-Term Support',
        image_url: imageFor('campaigns', 52, 'website-support-maintenance.jpg'),
        content: richText({
          title: 'Long-Term Website Support and Maintenance Package',
          intro:
            'The Long-Term Support package helps businesses keep their CMS-managed website stable after launch. The focus is practical maintenance, small improvements, content workflow support, and production issue response.',
          image: imageFor('campaigns', 53, 'website-support-maintenance-inline.jpg'),
          caption: 'Long-term support for CMS-managed websites',
          sections: [
            {
              heading: 'Support Activities',
              body:
                'Support includes checking public pages, CMS forms, API responses, media uploads, deployment stability, and content structure. Small improvements are handled with care so the codebase remains readable.',
              points: [
                'Bug fixing and deployment checks.',
                'CMS workflow adjustment.',
                'Content and media structure support.',
              ],
            },
            {
              heading: 'Maintenance Principle',
              body:
                'Production support should improve the platform without adding unnecessary complexity. Every change must be easy to understand and useful for the business.',
            },
          ],
          outcomes: [
            'More stable production operations.',
            'Cleaner CMS usage over time.',
            'A healthier website after launch.',
          ],
        }),
      },
    ];

    await Promise.all(
      campaignItems.map((campaign) =>
        upsertBy(
          Campaign,
          { slug: toSlug(campaign.title) },
          {
            title: campaign.title,
            slug: toSlug(campaign.title),
            content: campaign.content,
            image_url: campaign.image_url,
            category: campaign.category,
            is_published: true,
          }
        )
      )
    );

    const careers = [
      {
        job_title: 'Frontend Developer Intern',
        category: 'Internship',
        thumbnail: imageFor('careers', 54, 'frontend-developer-intern.jpg'),
        job_description:
          'Build and maintain responsive Nuxt/Vue user interfaces for CMS-managed pages, public website sections, and admin dashboard workflows. Responsibilities include implementing clean components, integrating API data, checking responsive behavior, and collaborating with backend developers. Requirements: strong HTML, CSS, JavaScript fundamentals, basic Vue or Nuxt experience, willingness to write readable code, and a portfolio or GitHub project. Location: Remote or hybrid in Indonesia. Work type: Internship.',
      },
      {
        job_title: 'Backend Developer Intern',
        category: 'Internship',
        thumbnail: imageFor('careers', 55, 'backend-developer-intern.jpg'),
        job_description:
          'Support backend API development for CMS content, authentication, file upload, contact messages, career applications, and dashboard data. Responsibilities include writing focused Express routes, Sequelize models, request validation, and safe error handling. Requirements: JavaScript fundamentals, basic SQL knowledge, interest in API design, and ability to keep code simple and readable. Location: Remote or hybrid in Indonesia. Work type: Internship.',
      },
      {
        job_title: 'UI/UX Designer Intern',
        category: 'Design',
        thumbnail: imageFor('careers', 56, 'ui-ux-designer-intern.jpg'),
        job_description:
          'Design practical web interfaces for company profile pages, admin dashboards, product catalogs, campaign pages, and career workflows. Responsibilities include wireframes, UI layout, component states, responsive considerations, and handoff notes for developers. Requirements: basic Figma skills, understanding of layout hierarchy, attention to spacing and typography, and a portfolio of interface work. Location: Remote or hybrid in Indonesia. Work type: Internship.',
      },
      {
        job_title: 'Quality Assurance Intern',
        category: 'Quality Assurance',
        thumbnail: imageFor('careers', 57, 'quality-assurance-intern.jpg'),
        job_description:
          'Help verify public pages, CMS forms, API-connected workflows, and responsive behavior before release. Responsibilities include preparing test notes, checking forms, validating content flows, reporting bugs clearly, and retesting fixes. Requirements: detail-oriented mindset, basic understanding of web applications, clear written communication, and willingness to learn structured testing. Location: Remote or hybrid in Indonesia. Work type: Internship.',
      },
      {
        job_title: 'Digital Operations Associate',
        category: 'Business & Operations',
        thumbnail: imageFor('careers', 58, 'digital-operations-associate.jpg'),
        job_description:
          'Support CMS content operations, website updates, campaign publication, career listing checks, and client communication preparation. Responsibilities include reviewing content accuracy, organizing media assets, coordinating publishing schedules, and documenting operational feedback. Requirements: strong communication, organized working style, comfort with web dashboards, and interest in digital business workflows. Location: Indonesia. Work type: Part-time or project-based.',
      },
    ];

    await Promise.all(
      careers.map((career) =>
        upsertBy(Career, { job_title: career.job_title }, {
          thumbnail: career.thumbnail,
          job_description: career.job_description,
          categoryId: getCategoryId(categoryMap, 'Career', career.category),
        })
      )
    );

    const contactMessages = [
      {
        name: 'Website Content Review',
        email: 'hello@havorsmartadigital.com',
        subject: 'Internal CMS seed verification',
        message:
          'This message verifies that the contact inbox menu has production-safe internal sample data after running the realistic seed. Replace this record with real inquiries when the website starts receiving submissions.',
        is_read: true,
      },
      {
        name: 'Project Inquiry Workflow',
        email: 'hello@havorsmartadigital.com',
        subject: 'CMS inquiry workflow check',
        message:
          'This internal record confirms that project inquiry data can be displayed in the admin inbox. It is intentionally owned by the company account so production seeding does not create a fake external lead.',
        is_read: false,
      },
    ];

    await Promise.all(
      contactMessages.map((message) =>
        upsertBy(ContactMessage, { email: message.email, subject: message.subject }, message)
      )
    );

    await upsertBy(
      CareerApplication,
      {
        email: 'hello@havorsmartadigital.com',
        position: 'Frontend Developer Intern',
      },
      {
        full_name: 'Havor Recruitment Verification',
        phone: '+62 812-0000-0000',
        address: 'Indonesia',
        latest_education: 'Internal CMS verification record',
        experience_summary:
          'This internal record verifies that the career application menu is connected after seeding. Remove it when live applicant data is available.',
        portfolio_url: 'https://havorsmarta.vercel.app/careers',
        message:
          'Internal production-safe verification entry for the career application workflow.',
        cv_original_name: 'havor-recruitment-verification.pdf',
        cv_mime_type: 'application/pdf',
        cv_size: 0,
        cv_storage_key: null,
        cv_bucket: null,
        cv_url: null,
        cv_signed_url_strategy: null,
        status: 'reviewed',
      }
    );

    console.log('Realistic production seed completed successfully.');
    console.log(`Seeded ${categorySeeds.length} categories across Product, News, Campaign, and Career.`);
    console.log('Images were copied from frontend public/images/banner into backend uploads folders.');
  } catch (error) {
    console.error('Realistic production seed failed:', error);
    process.exitCode = 1;
  } finally {
    await sequelize.close();
  }
};

seed();
