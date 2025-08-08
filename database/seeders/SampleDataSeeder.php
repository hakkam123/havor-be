<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\Service;
use App\Models\Product;
use App\Models\HomepageFeature;
use App\Models\Project;
use App\Models\Article;
use App\Models\Lead;
use App\Models\Clients;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            [
                'name' => 'Technology',
            ],
            [
                'name' => 'Healthcare',
            ],
            [
                'name' => 'Finance',
            ],
            [
                'name' => 'Education',
            ],
            [
                'name' => 'E-commerce',
            ]
        ];

        foreach ($industries as $industry) {
            Industry::firstOrCreate(['name' => $industry['name']], $industry);
        }

        $services = [
            [
                'name' => 'Web Development',
                'description' => 'Full-stack web application development using modern frameworks',
                'features' => 'Responsive Design, Progressive Web Apps, API Development, Database Design',
                'price' => 5000.00,
                'duration' => '4-8 weeks',
                'is_featured' => true
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Cross-platform mobile application development for iOS and Android',
                'features' => 'Native Performance, Cross-platform, App Store Publishing, Push Notifications',
                'price' => 8000.00,
                'duration' => '6-12 weeks',
                'is_featured' => true
            ],
            [
                'name' => 'Digital Marketing',
                'description' => 'Comprehensive digital marketing strategies and campaigns',
                'features' => 'SEO Optimization, Social Media Marketing, PPC Campaigns, Analytics',
                'price' => 2500.00,
                'duration' => '3-6 months',
                'is_featured' => false
            ],
            [
                'name' => 'UI/UX Design',
                'description' => 'User-centered design for web and mobile applications',
                'features' => 'User Research, Wireframing, Prototyping, Design Systems',
                'price' => 3500.00,
                'duration' => '3-6 weeks',
                'is_featured' => true
            ],
            [
                'name' => 'Cloud Solutions',
                'description' => 'Cloud infrastructure setup and migration services',
                'features' => 'AWS/Azure Setup, Migration Services, DevOps, Monitoring',
                'price' => 6000.00,
                'duration' => '4-10 weeks',
                'is_featured' => false
            ]
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }

        $products = [
            [
                'name' => 'Havor CMS Pro',
                'main_description' => 'Professional content management system with advanced features for enterprise-level websites and applications.',
                'hero_image_url' => 'https://via.placeholder.com/1200x600/0066cc/ffffff?text=Havor+CMS+Pro',
                'content_image_url' => 'https://via.placeholder.com/800x500/f8f9fa/333333?text=CMS+Dashboard',
                'content_description' => 'Our CMS Pro includes multi-user support, advanced SEO tools, e-commerce integration, custom themes, and a powerful API for seamless third-party integrations.'
            ],
            [
                'name' => 'Business Analytics Dashboard',
                'main_description' => 'Real-time business intelligence and analytics platform for data-driven decision making.',
                'hero_image_url' => 'https://via.placeholder.com/1200x600/28a745/ffffff?text=Analytics+Dashboard',
                'content_image_url' => 'https://via.placeholder.com/800x500/f8f9fa/333333?text=Analytics+Charts',
                'content_description' => 'Features include real-time reporting, custom dashboards, advanced data visualization, predictive analytics, and comprehensive API integration for all your business systems.'
            ],
            [
                'name' => 'E-commerce Platform Suite',
                'main_description' => 'Complete e-commerce solution with inventory management, payment processing, and customer analytics.',
                'hero_image_url' => 'https://via.placeholder.com/1200x600/dc3545/ffffff?text=E-commerce+Suite',
                'content_image_url' => 'https://via.placeholder.com/800x500/f8f9fa/333333?text=Shopping+Cart',
                'content_description' => 'Full-featured e-commerce platform with product catalog management, secure payment gateway integration, inventory tracking, customer management, and detailed sales analytics.'
            ]
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['name' => $product['name']], $product);
        }

        $features = [
            [
                'title' => 'Expert Team',
                'description' => 'Our team of experienced developers and designers deliver high-quality solutions',
                'icon_url' => 'bi bi-people',
                'order_index' => 1
            ],
            [
                'title' => '24/7 Support',
                'description' => 'Round-the-clock customer support to help you when you need it most',
                'icon_url' => 'bi bi-headset',
                'order_index' => 2
            ],
            [
                'title' => 'Fast Delivery',
                'description' => 'Quick turnaround times without compromising on quality',
                'icon_url' => 'bi bi-lightning',
                'order_index' => 3
            ],
            [
                'title' => 'Scalable Solutions',
                'description' => 'Future-proof solutions that grow with your business',
                'icon_url' => 'bi bi-arrow-up-right',
                'order_index' => 4
            ]
        ];

        foreach ($features as $feature) {
            HomepageFeature::firstOrCreate(['title' => $feature['title']], $feature);
        }

        $clients = [
            [
                'title' => 'TechCorp Industries',
                'description' => 'Leading technology company specializing in enterprise software solutions and digital transformation services.',
                'icon_url' => 'https://via.placeholder.com/100x100/0066cc/ffffff?text=TC'
            ],
            [
                'title' => 'MediCare Solutions',
                'description' => 'Healthcare technology provider focused on improving patient care through innovative medical software.',
                'icon_url' => 'https://via.placeholder.com/100x100/28a745/ffffff?text=MC'
            ],
            [
                'title' => 'RetailCorp Global',
                'description' => 'International retail chain with both physical stores and e-commerce presence across multiple markets.',
                'icon_url' => 'https://via.placeholder.com/100x100/dc3545/ffffff?text=RC'
            ],
            [
                'title' => 'EduTech Innovations',
                'description' => 'Educational technology company developing e-learning platforms and digital classroom solutions.',
                'icon_url' => 'https://via.placeholder.com/100x100/ffc107/ffffff?text=ET'
            ],
            [
                'title' => 'FinanceFirst Bank',
                'description' => 'Modern digital bank offering comprehensive financial services and innovative banking solutions.',
                'icon_url' => 'https://via.placeholder.com/100x100/17a2b8/ffffff?text=FF'
            ]
        ];

        foreach ($clients as $client) {
            Clients::firstOrCreate(['title' => $client['title']], $client);
        }

        $techIndustry = Industry::where('title', 'Technology')->first();
        $webService = Service::where('name', 'Web Development')->first();
        $mobileService = Service::where('name', 'Mobile App Development')->first();
        $uiService = Service::where('name', 'UI/UX Design')->first();
        
        $techCorpClient = Clients::where('name', 'TechCorp Industries')->first();
        $mediCareClient = Clients::where('title', 'MediCare Solutions')->first();
        $retailCorpClient = Clients::where('title', 'RetailCorp Global')->first();
        $eduTechClient = Clients::where('title', 'EduTech Innovations')->first();
        
        if ($techIndustry && $webService && $techCorpClient && $mediCareClient) {
            // Projects data (updated with client_id and proper relations)
            $projects = [
                [
                    'title' => 'E-commerce Platform for TechCorp',
                    'description' => 'Complete e-commerce solution with payment integration and inventory management',
                    'content' => 'Developed a comprehensive e-commerce platform featuring user authentication, product catalog, shopping cart, payment processing, and admin dashboard. The solution includes inventory management, order tracking, and customer support features.',
                    'image_url' => 'https://via.placeholder.com/600x400/0066cc/ffffff?text=E-commerce+Platform',
                    'client_id' => $techCorpClient->id,
                    'client_name' => $techCorpClient->title,
                    'service_id' => $webService->id,
                    'project_date' => '2024-01-15',
                    'status' => 'completed'
                ],
                [
                    'title' => 'Healthcare Management System',
                    'description' => 'Patient management and appointment scheduling system for medical practices',
                    'content' => 'Built a comprehensive healthcare management system that streamlines patient registration, appointment scheduling, medical records management, and billing processes.',
                    'image_url' => 'https://via.placeholder.com/600x400/28a745/ffffff?text=Healthcare+System',
                    'client_id' => $mediCareClient->id,
                    'client_name' => $mediCareClient->title,
                    'service_id' => $webService->id,
                    'project_date' => '2024-02-20',
                    'status' => 'in_progress'
                ]
            ];

            if ($retailCorpClient && $mobileService) {
                $projects[] = [
                    'title' => 'Mobile Shopping App',
                    'description' => 'Cross-platform mobile application for seamless shopping experience',
                    'content' => 'Developed a feature-rich mobile shopping application with product browsing, wishlist management, secure checkout, push notifications, and loyalty program integration.',
                    'image_url' => 'https://via.placeholder.com/600x400/dc3545/ffffff?text=Mobile+Shopping+App',
                    'client_id' => $retailCorpClient->id,
                    'client_name' => $retailCorpClient->title,
                    'service_id' => $mobileService->id,
                    'project_date' => '2024-03-10',
                    'status' => 'completed'
                ];
            }

            if ($eduTechClient && $uiService) {
                $projects[] = [
                    'title' => 'E-Learning Platform Design',
                    'description' => 'User-friendly interface design for online education platform',
                    'content' => 'Created an intuitive and engaging UI/UX design for an e-learning platform, focusing on user experience, accessibility, and mobile responsiveness to enhance student engagement.',
                    'image_url' => 'https://via.placeholder.com/600x400/ffc107/ffffff?text=E-Learning+Design',
                    'client_id' => $eduTechClient->id,
                    'client_name' => $eduTechClient->title,
                    'service_id' => $uiService->id,
                    'project_date' => '2024-04-05',
                    'status' => 'in_progress'
                ];
            }

            foreach ($projects as $project) {
                Project::firstOrCreate(['title' => $project['title']], $project);
            }

            // Articles data (updated with proper relations)
            $articles = [
                [
                    'title' => 'The Future of Web Development in 2024',
                    'short_description' => 'Exploring the latest trends and technologies shaping web development',
                    'content' => 'Web development continues to evolve rapidly with new frameworks, tools, and methodologies emerging constantly. In this article, we explore the key trends that will define web development in 2024, including the rise of AI-powered development tools, the growing importance of web performance, and the shift towards more sustainable coding practices.',
                    'image_url' => 'https://via.placeholder.com/800x400/0066cc/ffffff?text=Web+Development+2024',
                    'author' => 'Havor Team',
                    'industry_id' => $techIndustry->id,
                    'services_id' => $webService->id
                ],
                [
                    'title' => 'Building Scalable E-commerce Solutions',
                    'short_description' => 'Best practices for creating e-commerce platforms that can handle growth',
                    'content' => 'Creating an e-commerce platform that can scale with your business requires careful planning and the right technology stack. This article covers essential considerations for building scalable e-commerce solutions, from database design to payment processing and inventory management.',
                    'image_url' => 'https://via.placeholder.com/800x400/28a745/ffffff?text=Scalable+E-commerce',
                    'author' => 'Havor Development Team',
                    'industry_id' => $techIndustry->id,
                    'services_id' => $webService->id
                ]
            ];

            if ($mobileService) {
                $articles[] = [
                    'title' => 'Mobile App Development: iOS vs Android',
                    'short_description' => 'Comprehensive guide to choosing the right platform for your mobile app',
                    'content' => 'When developing a mobile application, choosing between iOS and Android development can be challenging. This article provides insights into platform-specific considerations, development costs, market reach, and cross-platform alternatives.',
                    'image_url' => 'https://via.placeholder.com/800x400/dc3545/ffffff?text=Mobile+Development',
                    'author' => 'Havor Mobile Team',
                    'industry_id' => $techIndustry->id,
                    'services_id' => $mobileService->id
                ];
            }

            foreach ($articles as $article) {
                Article::firstOrCreate(['title' => $article['title']], $article);
            }
        }

        $leads = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@startupventure.com',
                'message' => 'We need a web application for our startup. Looking for a modern, scalable solution with user authentication and payment integration.'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@retailcorp.com',
                'message' => 'Interested in e-commerce platform development. Need integration with existing inventory system and multi-vendor support.'
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'mike.brown@techsolutions.com',
                'message' => 'Looking for mobile app development services. iOS and Android required with real-time notifications and offline functionality.'
            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emma.davis@educationplus.org',
                'message' => 'We need a learning management system for our educational institution. Features should include course management, student progress tracking, and video conferencing.'
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@financegroup.com',
                'message' => 'Seeking cloud migration services for our financial platform. We need secure, compliant solutions with high availability and disaster recovery.'
            ]
        ];

        foreach ($leads as $lead) {
            Lead::firstOrCreate(['email' => $lead['email']], $lead);
        }
    }
}
