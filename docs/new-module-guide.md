# New Module Guide

Panduan membuat modul baru dengan pola `Shared + Feature`.

## Struktur Modul

Gunakan pola berikut:

```text
app/
└── Modules/
    └── FeatureName/
        ├── Controllers/
        ├── DTOs/
        ├── Requests/
        ├── Resources/
        ├── Repositories/
        │   └── Contracts/
        ├── Services/
        └── Routes/
```

## Langkah Membuat Modul Baru

1. Buat folder modul di `app/Modules/{ModuleName}`.
2. Tambahkan DTO untuk input create/update.
3. Tambahkan Form Request untuk validasi.
4. Tambahkan Resource untuk format response.
5. Tambahkan repository interface dan implementasi.
6. Tambahkan service untuk logika bisnis.
7. Tambahkan controller untuk endpoint HTTP.
8. Tambahkan route file dan include ke `routes/api.php`.
9. Bind interface repository ke implementasinya di `AppServiceProvider`.

## Konvensi yang Dipakai

- Controller hanya menangani request/response.
- Service menangani business logic.
- Repository menangani akses data.
- DTO menjadi jembatan data dari request ke service/repository.
- Response list paginated memakai helper shared dengan `meta`.
- Filter query sebaiknya dproductpatkan di repository, bukan controller.
- Permission/ACL sebaiknya dipusatkan di middleware dan policy, bukan menumpuk di controller.

## Repository Pattern

Pola yang dipakai saat ini adalah:

- `BaseRepository` menyediakan CRUD dan pagination umum.
- Repository per modul menambahkan aturan spesifik modul.
- Jika query masih standar, cukup pakai behavior dari `BaseRepository`.
- Jika butuh filter tambahan, override hook seperti:
  - `query()`
  - `applyCustomFilters()`
  - `searchableColumns()`
  - `sortableColumns()`

Contoh pada modul `sale`:

- `status` difilter di `SaleRepository`
- `date range` difilter di `SaleRepository`
- `search` dan `sorting` juga diatur dari repository modul

## Sale Module Notes

Modul `sale` memakai enum `SaleStatus` untuk status:

- `PAID`
- `UNPAID`

Alurnya:

- Request memvalidasi enum status
- Model melakukan cast ke enum
- Resource mengembalikan string value agar response API tetap konsisten

## Auth dan ACL Pattern

Kalau modul baru butuh permission:

- gunakan middleware untuk pembatasan role level tinggi
- gunakan policy untuk aturan yang bergantung pada actor dan target model
- hindari menaruh seluruh logika izin langsung di controller

Pola ini dipakai di modul `User`:

- `role:super_admin` membatasi akses admin-only
- `UserPolicy` mengizinkan `member` hanya mengubah profil sendiri

## Contoh Modul

Modul yang sudah ada:

- `User`
- `Product`
- `Sale`
- `SaleDetail`
- `Payment`

## Checklist

- Route sudah terdaftar
- Interface repository sudah di-bind
- Resource sudah dipakai di controller
- Pagination response sudah memakai meta
- Upload file disimpan di service, bukan controller
- Filter kompleks dproductpatkan di repository
- Status enum menggunakan enum class, bukan string bebas
