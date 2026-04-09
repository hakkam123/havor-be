# Havor Smarta Technology Backend

Backend API untuk Company Profile PT Havor Smarta Technology yang dibangun dengan **Express.js**, **MySQL**, dan **Sequelize**. Sistem ini dioptimalkan untuk performa tinggi menggunakan arsitektur **Raw SQL Query**.

## Fitur Utama
- **Autentikasi Admin**: Akses aman dengan JWT (Access Token & Refresh Token).
- **Pengelolaan Berita (News)**: CRUD artikel berita dengan sistem slug otomatis.
- **Hero Banner Management**: Banner dinamis (Gambar/Video) di setiap halaman.
- **Expertise (Services)**: Pengelolaan layanan utama perusahaan.
- **Katalog Produk**: Manajemen produk dengan relasi kategori.
- **Portofolio (Works)**: Katalog hasil kerja/proyek perusahaan.
- **Pesan Kontak**: API untuk menerima pesan dari pengunjung website.
- **High Performance**: Menggunakan Raw SQL murni untuk akses database yang super cepat.
- **Shared Category System**: Satu tabel kategori yang diintegrasikan untuk Produk dan Portofolio.

## Tech Stack
- **Node.js**: Runtime environment.
- **Express.js**: Web framework.
- **MySQL**: Database.
- **Sequelize**: ORM (digunakan untuk definisi skema & sinking).
- **JWT**: Keamanan autentikasi.
- **Multer**: Penanganan upload file (Gambar & Video).
- **Slugify**: Pembuatan slug otomatis untuk URL berita.

## Instalasi

1. Clone repository ini.
2. Instal dependensi:
   ```bash
   npm install
   ```
3. Konfigurasi Environment:
   Buat file `.env` di root directory (contoh ada di bawah).
4. Jalankan Server:
   ```bash
   npm run dev
   ``
---
Developed by **PT Havor Smarta Technology Team**.
