# API Routes

Dokumen ini berisi daftar endpoint backend yang sudah dikelompokkan berdasarkan akses Public vs Admin, lengkap dengan contoh request frontend dan contoh response.

## Base URL

```text
http://localhost:5000
```

## Konsep Akses

- Public: bisa diakses tanpa token.
- Admin (Protected): wajib header `Authorization: Bearer <accessToken>`.

## Access Token vs Refresh Token

Backend menggunakan 2 token:

- Access token untuk akses endpoint protected.
- Refresh token untuk meminta access token baru saat access token expired.

Konfigurasi default:

```text
JWT_EXPIRES_IN=15m
JWT_REFRESH_EXPIRES_IN=120m
```

Flow frontend:

1. Login ke `/api/auth/login` dan simpan `accessToken` + `refreshToken`.
2. Pakai `accessToken` untuk endpoint Admin.
3. Kalau access token expired, panggil `/api/auth/refresh` dengan `refreshToken`.
4. Simpan token baru dari response.
5. Kalau refresh token juga expired, user harus login ulang.

## Contoh Kode Frontend

### 1) Public GET

```js
const response = await fetch(`${API_URL}/api/careers`);
if (!response.ok) throw new Error('Failed to fetch careers');
const data = await response.json();
```

### 2) Admin JSON Request

```js
const response = await fetch(`${API_URL}/api/categories`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${accessToken}`,
  },
  body: JSON.stringify({ name: 'UI/UX Design' }),
});
const data = await response.json();
```

### 3) Admin Upload (multipart/form-data)

```js
const formData = new FormData();
formData.append('job_title', 'Frontend Developer');
formData.append('job_description', 'Build and maintain web interfaces');
formData.append('thumbnail', fileInput.files[0]);

const response = await fetch(`${API_URL}/api/careers`, {
  method: 'POST',
  headers: {
    Authorization: `Bearer ${accessToken}`,
  },
  body: formData,
});
const data = await response.json();
```

### 4) Refresh Token

```js
const response = await fetch(`${API_URL}/api/auth/refresh`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({ refreshToken }),
});
const data = await response.json();
```

## Ringkasan Endpoint (Public vs Admin)

| Method | Endpoint | Access | Content-Type |
|---|---|---|---|
| POST | /api/auth/login | Public | application/json |
| POST | /api/auth/refresh | Public | application/json |
| GET | /api/banners | Public | - |
| GET | /api/banners/:page | Public | - |
| POST | /api/banners | Admin | multipart/form-data |
| PUT | /api/banners/:id | Admin | multipart/form-data |
| DELETE | /api/banners/:id | Admin | - |
| GET | /api/categories | Public | - |
| POST | /api/categories | Admin | application/json |
| PUT | /api/categories/:id | Admin | application/json |
| DELETE | /api/categories/:id | Admin | - |
| GET | /api/clients | Public | - |
| POST | /api/clients | Admin | multipart/form-data |
| PUT | /api/clients/:id | Admin | multipart/form-data |
| DELETE | /api/clients/:id | Admin | - |
| GET | /api/careers | Public | - |
| POST | /api/careers | Admin | multipart/form-data |
| PUT | /api/careers/:id | Admin | multipart/form-data |
| DELETE | /api/careers/:id | Admin | - |
| GET | /api/expertise | Public | - |
| POST | /api/expertise | Admin | multipart/form-data |
| PUT | /api/expertise/:id | Admin | multipart/form-data |
| DELETE | /api/expertise/:id | Admin | - |
| GET | /api/news | Public | - |
| GET | /api/news/:slug | Public | - |
| POST | /api/news | Admin | multipart/form-data |
| PUT | /api/news/:id | Admin | multipart/form-data |
| DELETE | /api/news/:id | Admin | - |
| GET | /api/products | Public | - |
| POST | /api/products | Admin | multipart/form-data |
| PUT | /api/products/:id | Admin | multipart/form-data |
| DELETE | /api/products/:id | Admin | - |
| GET | /api/works | Public | - |
| POST | /api/works | Admin | multipart/form-data |
| PUT | /api/works/:id | Admin | multipart/form-data |
| DELETE | /api/works/:id | Admin | - |
| POST | /api/contact | Public | application/json |
| GET | /api/contact | Admin | - |
| PUT | /api/contact/:id/read | Admin | - |
| DELETE | /api/contact/:id | Admin | - |

## Detail per Resource

### Auth Routes

#### POST /api/auth/login

Access: Public  
Body (JSON):

```json
{
  "email": "admin@example.com",
  "password": "your-password"
}
```

Success response:

```json
{
  "id": 1,
  "username": "Admin",
  "email": "admin@example.com",
  "accessToken": "eyJhbGciOi...",
  "refreshToken": "eyJhbGciOi..."
}
```

#### POST /api/auth/refresh

Access: Public  
Body (JSON):

```json
{
  "refreshToken": "eyJhbGciOi..."
}
```

Success response:

```json
{
  "accessToken": "eyJhbGciOi...",
  "refreshToken": "eyJhbGciOi..."
}
```

### Banner Routes

#### GET /api/banners

Access: Public  
Success response:

```json
[
  {
    "id": 1,
    "page_name": "home",
    "title": "Homepage Banner",
    "subtitle": "Subtitle",
    "media_url": "/uploads/banners/banner.jpg",
    "media_type": "image"
  }
]
```

#### POST /api/banners

Access: Admin (Bearer Token)  
Body (multipart): `page_name`, `title`, `subtitle`, `media_type`, `media_url` (file/url)

Success response:

```json
{
  "id": 2,
  "page_name": "about",
  "title": "About Us",
  "subtitle": "Who we are",
  "media_url": "/uploads/banners/about-banner.jpg",
  "media_type": "image"
}
```

### Category Routes

#### GET /api/categories

Access: Public

```json
[
  {
    "id": 1,
    "name": "Web Development"
  }
]
```

#### POST /api/categories

Access: Admin (Bearer Token)  
Body (JSON):

```json
{
  "name": "UI/UX Design"
}
```

```json
{
  "id": 2,
  "name": "UI/UX Design"
}
```

#### PUT /api/categories/:id

Access: Admin (Bearer Token)  
Body (JSON):

```json
{
  "name": "Brand Design"
}
```

```json
{
  "message": "Category updated successfully"
}
```

### Client Routes

#### GET /api/clients

Access: Public

```json
[
  {
    "id": 1,
    "name": "Global Partner",
    "client_icon": "/uploads/clients/client-icon.png",
    "description": "Long-term technology partner"
  }
]
```

#### POST /api/clients

Access: Admin (Bearer Token)  
Body (multipart): `name`, `description`, `client_icon` (file)

```json
{
  "id": 2,
  "name": "New Client",
  "client_icon": "/uploads/clients/new-client.png",
  "description": "Project partner"
}
```

#### PUT /api/clients/:id

Access: Admin (Bearer Token)  
Body (multipart): `name`, `description`, `client_icon` (optional file)

```json
{
  "message": "Client updated successfully"
}
```

### Career Routes

#### GET /api/careers

Access: Public

```json
[
  {
    "id": 1,
    "thumbnail": "/uploads/careers/career-thumb.jpg",
    "job_title": "Frontend Developer",
    "job_description": "Build and maintain web interfaces"
  }
]
```

#### POST /api/careers

Access: Admin (Bearer Token)  
Body (multipart): `job_title`, `job_description`, `thumbnail` (file)

```json
{
  "id": 2,
  "thumbnail": "/uploads/careers/ui-designer.jpg",
  "job_title": "UI Designer",
  "job_description": "Design modern digital experiences"
}
```

#### PUT /api/careers/:id

Access: Admin (Bearer Token)  
Body (multipart): `job_title`, `job_description`, `thumbnail` (optional file)

```json
{
  "message": "Career updated successfully"
}
```

### Expertise Routes

#### GET /api/expertise

Access: Public

```json
[
  {
    "id": 1,
    "name": "Frontend Development",
    "description": "React, responsive UI, and modern interfaces",
    "icon_url": "/uploads/expertise/frontend.svg"
  }
]
```

#### POST /api/expertise

Access: Admin (Bearer Token)  
Body (multipart): `name`, `description`, `icon_url` (file)

```json
{
  "id": 2,
  "name": "DevOps",
  "description": "Deployment and automation",
  "icon_url": "/uploads/expertise/devops.png"
}
```

### News Routes

#### GET /api/news

Access: Public

```json
[
  {
    "id": 1,
    "title": "New Office Launch",
    "slug": "new-office-launch",
    "content": "We opened a new office...",
    "category": "Company",
    "is_published": true,
    "image_url": "/uploads/news/office.jpg"
  }
]
```

#### GET /api/news/:slug

Access: Public

```json
{
  "id": 1,
  "title": "New Office Launch",
  "slug": "new-office-launch",
  "content": "We opened a new office...",
  "category": "Company",
  "is_published": true,
  "image_url": "/uploads/news/office.jpg"
}
```

#### POST /api/news

Access: Admin (Bearer Token)  
Body (multipart): `title`, `content`, `category`, `is_published`, `image_url` (file)

```json
{
  "id": 2,
  "title": "Product Update",
  "slug": "product-update",
  "content": "We improved the dashboard...",
  "category": "Product",
  "is_published": true,
  "image_url": "/uploads/news/update.jpg"
}
```

### Product Routes

#### GET /api/products

Access: Public

```json
[
  {
    "id": 1,
    "name": "Mobile App Dashboard",
    "description": "A dashboard for monitoring operations",
    "image_url": "/uploads/products/dashboard.png",
    "external_link": "https://example.com/demo",
    "categoryId": 1,
    "category_name": "Web Development"
  }
]
```

#### POST /api/products

Access: Admin (Bearer Token)  
Body (multipart): `name`, `description`, `external_link`, `categoryId`, `image_url` (file)

```json
{
  "id": 2,
  "name": "Landing Page Kit",
  "description": "Reusable marketing sections",
  "image_url": "/uploads/products/landing-page.png",
  "external_link": "https://example.com/kit",
  "categoryId": 1
}
```

### Work Routes

#### GET /api/works

Access: Public

```json
[
  {
    "id": 1,
    "title": "Company Website Redesign",
    "description": "Full redesign and frontend rebuild",
    "client": "Havor Client",
    "year": 2025,
    "image_url": "/uploads/works/redesign.jpg",
    "categoryId": 1,
    "category_name": "Web Development"
  }
]
```

#### POST /api/works

Access: Admin (Bearer Token)  
Body (multipart): `title`, `description`, `client`, `year`, `categoryId`, `image_url` (file)

```json
{
  "id": 2,
  "title": "POS System",
  "description": "Point-of-sale platform",
  "client": "Retail Partner",
  "year": 2026,
  "image_url": "/uploads/works/pos.png",
  "categoryId": 2
}
```

### Contact Routes

#### POST /api/contact

Access: Public  
Body (JSON):

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "subject": "Project Inquiry",
  "message": "I want to discuss a website project."
}
```

```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "subject": "Project Inquiry",
  "message": "I want to discuss a website project."
}
```

#### GET /api/contact

Access: Admin (Bearer Token)

```json
[
  {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "subject": "Project Inquiry",
    "message": "I want to discuss a website project.",
    "is_read": false
  }
]
```

#### PUT /api/contact/:id/read

Access: Admin (Bearer Token)

```json
{
  "message": "Message marked as read"
}
```

## Response Error Umum

Error yang umum dari backend:

```json
{
  "message": "Not authorized, no token"
}
```

```json
{
  "message": "Not authorized, token failed"
}
```

```json
{
  "message": "Internal server error"
}
```

## Catatan Frontend

- Untuk endpoint upload, gunakan `FormData` dan jangan set `Content-Type` manual.
- URL file dari API berbentuk path relatif seperti `/uploads/news/file.jpg`.
- Di frontend, gabungkan dengan host backend, misalnya: `${API_URL}${image_url}`.
