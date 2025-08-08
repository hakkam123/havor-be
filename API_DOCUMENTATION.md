# Havor Backend API Documentation

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
This API uses Laravel Sanctum for authentication.

### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "admin@havor.com",
    "password": "password123"
}
```

Response:
```json
{
    "token": "your-sanctum-token",
    "user": {
        "id": 1,
        "name": "Admin Havor",
        "email": "admin@havor.com",
        "role": "admin"
    }
}
```

### Logout
```http
POST /api/logout
Authorization: Bearer your-sanctum-token
```

### Get User Info
```http
GET /api/user
Authorization: Bearer your-sanctum-token
```

## Public Endpoints (No Authentication Required)

### Contact Form (Leads)
```http
POST /api/leads
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "message": "I'm interested in your services"
}
```

### Public Data Access
- `GET /api/services/public` - Get all services
- `GET /api/projects/public` - Get all projects
- `GET /api/articles/public` - Get all articles
- `GET /api/industries/public` - Get all industries
- `GET /api/products/public` - Get all products
- `GET /api/homepage-features/public` - Get all homepage features

## Protected Endpoints (Authentication Required)

### Services
- `GET /api/services` - List all services
- `POST /api/services` - Create new service
- `GET /api/services/{id}` - Get specific service
- `PUT /api/services/{id}` - Update service
- `DELETE /api/services/{id}` - Delete service

### Projects
- `GET /api/projects` - List all projects
- `POST /api/projects` - Create new project
- `GET /api/projects/{id}` - Get specific project
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project

### Articles
- `GET /api/articles` - List all articles (with filters)
- `POST /api/articles` - Create new article
- `GET /api/articles/{id}` - Get specific article
- `PUT /api/articles/{id}` - Update article
- `DELETE /api/articles/{id}` - Delete article

### Industries
- `GET /api/industries` - List all industries
- `POST /api/industries` - Create new industry
- `GET /api/industries/{id}` - Get specific industry
- `PUT /api/industries/{id}` - Update industry
- `DELETE /api/industries/{id}` - Delete industry

### Products
- `GET /api/products` - List all products
- `POST /api/products` - Create new product
- `GET /api/products/{id}` - Get specific product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

### Homepage Features
- `GET /api/homepage-features` - List all homepage features
- `POST /api/homepage-features` - Create new homepage feature
- `GET /api/homepage-features/{id}` - Get specific homepage feature
- `PUT /api/homepage-features/{id}` - Update homepage feature
- `DELETE /api/homepage-features/{id}` - Delete homepage feature

### Leads Management (Admin Only)
- `GET /api/leads` - List all leads
- `GET /api/leads/{id}` - Get specific lead
- `DELETE /api/leads/{id}` - Delete lead

## Test Credentials
- **Admin**: admin@havor.com / password123
- **Editor**: editor@havor.com / password123

## Usage Example with curl

1. Login:
```bash
curl -X POST http://127.0.0.1:8000/api/login \
     -H "Content-Type: application/json" \
     -d '{"email": "admin@havor.com", "password": "password123"}'
```

2. Use the token from login response:
```bash
curl -X GET http://127.0.0.1:8000/api/services \
     -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

3. Create a service:
```bash
curl -X POST http://127.0.0.1:8000/api/services \
     -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     -H "Content-Type: application/json" \
     -d '{
         "title": "Web Development",
         "description": "Custom web development services",
         "icon_url": "https://example.com/icon.png",
         "order_index": 1
     }'
```
