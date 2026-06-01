# Backend Implementation Notes

## 1. Ringkasan Update Backend

Backend Careers sekarang menerima lamaran publik dengan upload CV PDF langsung ke Supabase Storage melalui S3-compatible API. File CV diproses di memory, diupload ke bucket private, lalu backend hanya menyimpan metadata dan `cv_storage_key` di database.

Contact Us tetap mengirim email konfirmasi ke pengirim dan notifikasi ke admin.

## 2. Endpoint yang Diupdate

### `POST /api/careers`

Public endpoint untuk lamaran kerja. Request harus memakai `multipart/form-data`.

Field utama:

- `fullName`
- `email`
- `phone`
- `position`
- `message` atau `coverLetter`
- `cv`

Field tambahan yang masih didukung:

- `address`
- `latestEducation`
- `experienceSummary`
- `portfolioUrl`

### `POST /api/contact`

Public endpoint Contact Us. Request memakai JSON:

- `name`
- `email`
- `subject`
- `message`

## 3. Cara Kerja Upload CV ke Supabase Storage

1. Request masuk ke `POST /api/careers`.
2. Multer membaca file `cv` memakai `memoryStorage`.
3. Backend memvalidasi tipe file dan ukuran file.
4. `storageService.uploadBuffer()` mengupload buffer ke Supabase Storage S3-compatible API.
5. Backend menyimpan data pelamar dan metadata CV ke tabel `career_applications`.
6. Backend mengirim email konfirmasi ke pelamar dan notifikasi ke admin.

Storage key memakai format:

```text
careers/<year>/<month>/<random-id>-<safe-original-filename>.pdf
```

Contoh:

```text
careers/2026/06/550e8400-cv-budi-santoso.pdf
```

## 4. Kenapa CV Tidak Disimpan di Server Express

Server Express tidak cocok menjadi tempat penyimpanan permanen CV karena:

- deployment cPanel/Node bisa mengganti atau membersihkan file lokal,
- file CV adalah data sensitif,
- object storage lebih tepat untuk akses privat, backup, dan rotasi credential,
- database cukup menyimpan metadata dan storage key.

## 5. Limit Upload CV Maksimal 2 MB

Limit backend:

```js
limits: { fileSize: 2 * 1024 * 1024 }
```

Jika file lebih besar dari 2 MB, API mengembalikan:

```json
{
  "success": false,
  "message": "Ukuran CV maksimal 2 MB."
}
```

## 6. Validasi File PDF

CV hanya diterima jika:

- extension file `.pdf`
- MIME type `application/pdf`

Jika file bukan PDF, API mengembalikan:

```json
{
  "success": false,
  "message": "CV harus berupa file PDF."
}
```

## 7. Environment Variables yang Dibutuhkan

```env
PORT=
NODE_ENV=
FRONTEND_URL=
ADMIN_EMAIL=

GMAIL_USER=
GMAIL_APP_PASSWORD=

OBJECT_STORAGE_ENDPOINT=https://bpnbjgdoiggfssbgzboo.storage.supabase.co/storage/v1/s3
OBJECT_STORAGE_REGION=ap-southeast-1
OBJECT_STORAGE_BUCKET=havor-cv
OBJECT_STORAGE_ACCESS_KEY_ID=
OBJECT_STORAGE_SECRET_ACCESS_KEY=
OBJECT_STORAGE_FORCE_PATH_STYLE=true
OBJECT_STORAGE_PUBLIC_BASE_URL=

DATABASE_URL=
```

Catatan:

- `OBJECT_STORAGE_ACCESS_KEY_ID` dan `OBJECT_STORAGE_SECRET_ACCESS_KEY` harus diisi dari Supabase Storage S3 Access Keys.
- Jangan commit `.env`.
- Bucket `havor-cv` harus private.
- `OBJECT_STORAGE_PUBLIC_BASE_URL` dikosongkan karena file CV private.

## 8. Cara Setup Supabase Storage

1. Buka project Supabase.
2. Masuk ke menu Storage.
3. Buat bucket private khusus CV.
4. Aktifkan/ambil S3-compatible endpoint dari Supabase.
5. Generate S3 access key.
6. Isi env backend.
7. Restart backend.

## 9. Cara Membuat Bucket `havor-cv`

1. Supabase Dashboard -> Storage.
2. Klik New bucket.
3. Nama bucket: `havor-cv`.
4. Pastikan bucket tidak public.
5. Simpan.

## 10. Cara Generate Supabase S3 Access Key

1. Supabase Dashboard -> Project Settings atau Storage Settings.
2. Cari bagian S3 Access Keys.
3. Generate access key baru.
4. Isi ke:

```env
OBJECT_STORAGE_ACCESS_KEY_ID=
OBJECT_STORAGE_SECRET_ACCESS_KEY=
```

Jika credential pernah terekspos, revoke key lama dan generate key baru.

## 11. Cara Setup Gmail App Password

1. Aktifkan 2-Step Verification di Google Account.
2. Buka App Passwords.
3. Buat app password untuk Mail.
4. Isi:

```env
GMAIL_USER=
GMAIL_APP_PASSWORD=
ADMIN_EMAIL=
```

Gunakan app password, bukan password login Gmail utama.

## 12. Cara Testing Careers

Test minimal:

1. `POST /api/careers` tanpa field.
2. `POST /api/careers` dengan email invalid.
3. `POST /api/careers` tanpa `cv`.
4. `POST /api/careers` dengan file non-PDF.
5. `POST /api/careers` dengan PDF lebih dari 2 MB.
6. `POST /api/careers` dengan PDF valid di bawah 2 MB.
7. Pastikan file masuk ke bucket `havor-cv`.
8. Pastikan tidak ada file CV permanen di folder `uploads`.
9. Pastikan database hanya menyimpan metadata dan `cv_storage_key`.
10. Pastikan pelamar dan admin menerima email.

## 13. Cara Testing Contact Us

Kirim JSON valid ke `POST /api/contact`:

```json
{
  "name": "QA User",
  "email": "qa@example.com",
  "subject": "Integration Test",
  "message": "Testing Contact Us"
}
```

Expected:

- status `201`,
- email konfirmasi ke pengirim,
- email notifikasi ke admin.

## 14. Notes Jika Gmail Belum Terkirim

Cek:

- `GMAIL_USER`,
- `GMAIL_APP_PASSWORD`,
- `ADMIN_EMAIL`,
- app password masih aktif,
- Gmail 2-Step Verification aktif,
- server cPanel tidak memblokir outbound SMTP.

API tidak menampilkan stack trace atau credential ke frontend.

## 15. Notes Jika Supabase Storage Belum Bisa Upload

Cek:

- bucket `havor-cv` sudah ada,
- bucket private,
- endpoint benar,
- region benar,
- access key dan secret key benar,
- `OBJECT_STORAGE_FORCE_PATH_STYLE=true`,
- server cPanel bisa akses HTTPS ke Supabase.

Jika upload gagal, response frontend tetap aman dan tidak menampilkan credential.

## 16. Cara Rotate Credential Jika Secret Key Terekspos

1. Revoke S3 access key lama di Supabase.
2. Generate S3 access key baru.
3. Update `.env` backend di cPanel.
4. Restart Node app.
5. Test upload CV valid.

Untuk Gmail:

1. Hapus app password lama di Google Account.
2. Generate app password baru.
3. Update `.env`.
4. Restart Node app.
5. Test Contact Us.

## 17. File yang Dibuat/Diubah

Backend:

- `src/middlewares/uploadMiddleware.js`
- `src/routes/careerRoutes.js`
- `src/controllers/careerController.js`
- `src/services/storageService.js`
- `src/services/emailService.js`
- `src/models/CareerApplication.js`
- `src/validations/requestSchemas.js`
- `.env.example`
- `BACKEND_IMPLEMENTATION_NOTES.md`
- `INTEGRATION_TESTING_REPORT.md`

Frontend:

- `app/services/careerService.ts`
- `app/pages/careers/index.vue`

## Deploy Notes Singkat

Frontend bisa dipush seperti biasa selama environment staging sudah punya `VITE_API_BASE_URL` yang mengarah ke backend API production/staging.

Backend saat upload ulang ke cPanel wajib dipastikan `.env` production berisi database, Gmail, CORS frontend, dan Supabase Storage S3-compatible env. Jangan upload file `.env` ke Git.
