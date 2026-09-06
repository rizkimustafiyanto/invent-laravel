# Installation Guide

Panduan ini dipakai saat project backend `inventory` di-clone ke environment baru.

## Prasyarat

- PHP 8.3+
- Composer
- Database MySQL/PostgreSQL/SQLite
- Node.js dan npm

## Langkah Instalasi

1. Clone repository.
2. Masuk ke folder `backend-sales`.
3. Install dependency PHP.
4. Copy file environment.
5. Generate application key.
6. Sesuaikan koneksi database di `.env`.
7. Jalankan migration dan seed bila perlu.
8. Buat symbolic link untuk storage jika ada upload file.

## Contoh Perintah

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

## Catatan

- Jika memakai product dengan upload image, pastikan folder `storage/app/public` dapat diakses melalui `public/storage`.
- Jika project memakai queue atau scheduler, pastikan service pendukung juga aktif.

