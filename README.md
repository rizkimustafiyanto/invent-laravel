# Sales Report Backend

Backend API untuk aplikasi `inventory` menggunakan Laravel dan struktur `module-based`.

## Dokumentasi

- [Backend Overview](docs/backend-overview.md)
- [Installation Guide](docs/installation-guide.md)
- [New Module Guide](docs/new-module-guide.md)

## Ringkas

- Shared code ada di `app/Modules/Shared`
- Feature code ada di `app/Modules/{Feature}`
- Endpoint API saat ini:
  - `/api/users`
  - `/api/products`
  - `/api/sales`
  - `/api/sale-details`
  - `/api/payments`

## Fitur Response

- Response standar sukses/gagal lewat helper shared
- Response list paginated memakai `meta`:
  - `page`
  - `limit`
  - `hasNext`
  - `hasPrevious`
  - `totalData`
  - `totalPage`

## Pola Repository

- `BaseRepository` menyediakan CRUD dan pagination umum
- Repository modul seperti `SaleRepository` dipakai untuk filter dan query spesifik
- Filter kompleks seperti `status`, `date range`, `search`, dan `sorting` diletakkan di repository, bukan controller

## Port Development

- `php artisan serve` memakai `SERVER_PORT`
- Default jika tidak diisi adalah `8000`
- Ubah nilai ini di `.env` jika ingin menjalankan backend di port lain

## ACL

- Role yang tersedia: `member`, `super_admin`
- `member` bisa mengubah profil sendiri
- `super_admin` bisa mengelola user penuh
