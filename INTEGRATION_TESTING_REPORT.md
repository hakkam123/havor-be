# Integration Testing Report

Date: 2026-06-01

## Scope

Tested the public frontend forms against the local backend on Laragon:

- Contact Us form
- Careers Apply modal form
- Frontend fetch target
- Gmail notification path
- Resume PDF upload validation
- Object storage handoff

## Local Environment

- Frontend: `http://127.0.0.1:3000`
- Backend API: `http://127.0.0.1:5000`
- Database: local MySQL via Laragon
- Frontend submit env used during test: `VITE_API_BASE_URL=http://127.0.0.1:5000`

## Result Summary

| Area | Status | Notes |
| --- | --- | --- |
| Contact form frontend validation | PASS | Empty and invalid email states show frontend validation messages after hydration. |
| Contact form submit | PASS | Browser submitted `POST http://127.0.0.1:5000/api/contact` and received `201`. |
| Career Apply modal | PASS | Apply button opens modal form with complete candidate fields. |
| Career frontend validation | PASS | Required name and invalid email validation appeared in modal. |
| Resume upload UI | PASS | Field label is `Upload Resume`; file input accepts PDF. |
| Career submit fetch target | PASS | Browser submitted `POST http://127.0.0.1:5000/api/careers` with multipart form data. |
| Backend contact validation | PASS | Invalid payloads return `422`. |
| Backend career validation | PASS | Missing fields, non-PDF file, and oversized PDF return controlled `422` responses. |
| Gmail send | PASS | Gmail env is configured; Contact API test returned `sender: true` and `admin: true`. |
| Object storage upload | PASS | Supabase Storage S3-compatible upload succeeded; CV stored under `careers/2026/06/...pdf`. |
| Signed URL read | PASS | Private CV object was readable through a temporary signed URL; response `200` with `application/pdf`. |
| Frontend build | PASS | `npm run build` completed successfully. |
| Backend module load | PASS | Storage, email, contact, career controllers/routes loaded successfully. |

## Browser Integration Evidence

Headless browser test covered:

- `/#contact`
  - Submit empty form.
  - Submit invalid email.
  - Submit valid contact payload.
  - Verified `POST http://127.0.0.1:5000/api/contact`.
  - Verified response status `201`.

- `/careers`
  - Clicked first `Apply Now` button.
  - Verified application modal opens.
  - Submit empty modal form.
  - Submit invalid email.
  - Attached a test PDF under 2 MB to the resume input.
  - Submit valid candidate payload.
  - Verified `POST http://127.0.0.1:5000/api/careers`.
  - Verified response status `201`.

Captured browser requests:

```json
[
  {
    "method": "POST",
    "url": "http://127.0.0.1:5000/api/contact",
    "resourceType": "fetch"
  },
  {
    "method": "GET",
    "url": "http://127.0.0.1:3000/havor/api/careers",
    "resourceType": "fetch"
  },
  {
    "method": "POST",
    "url": "http://127.0.0.1:5000/api/careers",
    "resourceType": "fetch"
  }
]
```

Captured browser responses:

```json
[
  {
    "method": "POST",
    "url": "http://127.0.0.1:5000/api/contact",
    "status": 201
  },
  {
    "method": "GET",
    "url": "http://127.0.0.1:3000/havor/api/careers",
    "status": 200
  },
  {
    "method": "POST",
    "url": "http://127.0.0.1:5000/api/careers",
    "status": 201
  }
]
```

The `GET /havor/api/careers` request is the existing frontend careers listing fetch. The public application submit is the `POST http://127.0.0.1:5000/api/careers` request.

## Direct API Evidence

Direct API tests returned:

| Case | Expected | Actual | Status |
| --- | ---: | ---: | --- |
| `POST /api/contact` empty JSON | 422 | 422 | PASS |
| `POST /api/contact` invalid email | 422 | 422 | PASS |
| `POST /api/contact` valid JSON | 201 | 201 | PASS |
| `POST /api/careers` empty multipart | 422 | 422 | PASS |
| `POST /api/careers` non-PDF resume | 422 | 422 | PASS |
| `POST /api/careers` PDF larger than 2 MB | 422 | 422 | PASS |
| `POST /api/careers` valid PDF under 2 MB | 201 | 201 | PASS |
| Signed URL read for stored CV | 200 | 200 | PASS |

Valid contact response after Gmail env was configured includes:

```json
{
  "success": true,
  "message": "Pesan berhasil dikirim. Mohon tunggu sebentar, admin akan membalas melalui email.",
  "data": {
    "email": {
      "sender": true,
      "admin": true
    }
  }
}
```

`sender: true` and `admin: true` confirm Gmail notification delivery was accepted by the mail service during local testing.

Valid career application with PDF returns:

```json
{
  "success": true,
  "message": "Lamaran berhasil dikirim. Mohon tunggu sebentar, admin akan membalas melalui email.",
  "data": {
    "cvStorageKey": "careers/2026/06/93ccf8a5178f5dbac88c-storage-qa-cv.pdf",
    "email": {
      "applicant": true,
      "admin": true
    }
  }
}
```

The local `uploads` file count did not change during CV submission, confirming the CV was not stored permanently on the Express server.

## Configuration Notes

Gmail notification is configured in local `.env` with:

```env
GMAIL_USER
GMAIL_APP_PASSWORD
ADMIN_EMAIL
```

Secret values are intentionally not documented in this report.

Object storage is configured locally with Supabase Storage S3-compatible API. Production/cPanel must use these env names:

```env
OBJECT_STORAGE_ENDPOINT=https://bpnbjgdoiggfssbgzboo.storage.supabase.co/storage/v1/s3
OBJECT_STORAGE_REGION=ap-southeast-1
OBJECT_STORAGE_BUCKET=havor-cv
OBJECT_STORAGE_ACCESS_KEY_ID=
OBJECT_STORAGE_SECRET_ACCESS_KEY=
OBJECT_STORAGE_FORCE_PATH_STYLE=true
OBJECT_STORAGE_PUBLIC_BASE_URL=
```

Secret values are intentionally not documented in this report. Bucket `havor-cv` must stay private.

## Fixes Applied During Testing

- Added CORS allowance for `http://127.0.0.1:3000` and related local origins.
- Ensured backend imports models before development sync so required tables exist.
- Preserved admin `POST /api/careers` behavior when Authorization header is present.
- Added public unauthenticated `POST /api/careers` application flow for the frontend form.
- Added PDF-only in-memory CV upload validation for public applications.
- Updated CV upload limit to 2 MB.
- Added storage service with Supabase Storage S3-compatible env.
- Added Gmail email service that skips safely when credentials are missing.
- Updated frontend `.env.example` so `VITE_API_BASE_URL` points to the backend port.

## Verification Commands

Frontend:

```bash
npm run build
```

Backend module load:

```bash
node -e "require('./src/services/storageService'); require('./src/services/emailService'); require('./src/controllers/careerController'); require('./src/controllers/contactController'); require('./src/routes/careerRoutes'); require('./src/routes/contactRoutes'); console.log('module-load-ok')"
```

## Final Notes

The forms are no longer console-only flows. Both public forms perform real fetch requests to the configured backend endpoint.

Gmail and Supabase Storage both passed local integration testing. For deployment, the remaining work is environment configuration on staging/cPanel, not code changes.
