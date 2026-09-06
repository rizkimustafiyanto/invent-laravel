# Backend Overview

`inventory` backend adalah API Laravel dengan struktur `module-based`.

## Tujuan Struktur

- Membuat fitur mudah dipisahkan per modul
- Menjaga shared code tetap rapi dan reusable
- Mempermudah scaling saat fitur makin banyak

## Shared Layer

Komponen umum berada di `app/Modules/Shared`:

- `DTOs/BaseDTO.php`
- `Repositories/BaseRepository.php`
- `Services/BaseService.php`
- `Contracts/RepositoryInterface.php`
- `Http/Controllers/Api/BaseController.php`
- `Http/Resources/BaseResource.php`
- `Traits/ApiResponse.php`

## Development Server Port

Laravel `php artisan serve` membaca port dari `SERVER_PORT`.

- Kalau `SERVER_PORT` tidak diisi, default-nya tetap `8000`
- Jika ingin pindah port, ubah nilai `SERVER_PORT` di `.env`
- `APP_PORT` tidak dipakai langsung oleh `artisan serve`

## Alur Layer

Alur kerja modul umumnya seperti ini:

1. Controller menerima request dan mengembalikan response.
2. Service menangani business logic dan transaksi.
3. Repository menangani akses data.
4. Model dan database menyimpan state.

Pola ini membuat controller tetap tipis dan memudahkan scaling.

## Repository Pattern

`BaseRepository` adalah fondasi umum untuk CRUD, pagination, dan hook query.

Repository per modul seperti `SaleRepository`, `ProductRepository`, `UserRepository`, `PaymentRepository`, dan `SaleDetailRepository` dipakai untuk aturan spesifik modul.

Perbedaan praktisnya:

- `BaseRepository` menyediakan perilaku umum.
- Repository modul override hook seperti:
  - `query()`
  - `applyCustomFilters()`
  - `searchableColumns()`
  - `sortableColumns()`

Contoh pada modul `sale`:

- `status` difilter di `SaleRepository`
- `date range` difilter di `SaleRepository`
- `search` dan `sorting` juga diatur dari repository modul

## Sale Status Enum

Modul `sale` memakai enum `App\Enums\SaleStatus` dengan value:

- `PAID`
- `UNPAID`

Pemakaiannya:

- Request validasi memakai `Rule::enum(SaleStatus::class)`
- Model `Sale` melakukan cast ke enum
- Resource mengembalikan string value supaya response API konsisten

## ACL dan Role

Project ini memakai dua role:

- `member`
- `super_admin`

Aturan akses utama:

- `member` bisa melihat dan mengubah profil sendiri melalui auth endpoint dan `users/{id}` yang sama dengan dirinya
- `member` tidak bisa melihat daftar user
- `member` tidak bisa membuat, menghapus, atau mengubah role user lain
- `super_admin` bisa mengelola user secara penuh

Implementasi teknis:

- Middleware `role:super_admin` dipakai untuk endpoint admin-only
- `UserPolicy` dipakai untuk aturan self-view dan self-update
- `AuthServiceProvider` mendaftarkan policy ke model `User`

## List Filter

Endpoint list sale mendukung:

- `limit`
- `status`
- `date_from`
- `date_to`
- `search`
- `sort_by`
- `sort_direction`

Contoh:

```text
GET /api/sales?limit=10&status=PAID
GET /api/sales?date_from=2026-07-01&date_to=2026-07-25
GET /api/sales?search=INV&sort_by=date&sort_direction=desc
```

## Sales Flow

Create sale sekarang menggunakan payload:

```json
{
  "date": "2026-07-26",
  "products": [
    {
      "product_id": "uuid-product-1",
      "qty": 2
    },
    {
      "product_id": "uuid-product-2",
      "qty": 3
    }
  ]
}
```

Backend akan:

- create sales header
- generate `code`
- insert `sale_details`
- hitung `total_qty`
- hitung `total_amount`
- update `sales.status`

## Export dan Import

Modul yang menyediakan file handling:

- `Product`
  - `GET /api/products-export`
  - `GET /api/products-template`
  - `POST /api/products-import`
- `Sale`
  - `GET /api/sales-export`
- `AuditLog`
  - `GET /api/audit-logs`

Format yang dipakai saat ini adalah `CSV` supaya tetap ringan dan tanpa dependency tambahan.

## Payment Flow

Create payment sekarang menggunakan payload:

```json
{
  "sale_id": "uuid-sale-1",
  "date": "2026-07-26",
  "payment_method": "CASH"
}
```

Backend akan:

- generate payment `code`
- set `amount` dari total sale
- insert payment
- update `sales.status = PAID`

Jika payment dihapus:

- backend cek payment lain pada sale yang sama
- jika tidak ada payment lain, `sales.status` kembali `UNPAID`

## Modul Saat Ini

- `User`
- `Product`
- `Sale`
- `SaleDetail`
- `Payment`

## Response Standard

Response sukses umum:

```json
{
  "success": true,
  "message": "Success",
  "data": null,
  "errors": null
}
```

Response paginated:

```json
{
  "success": true,
  "message": "Success",
  "data": [],
  "errors": null,
  "meta": {
    "page": 1,
    "limit": 10,
    "hasNext": false,
    "hasPrevious": false,
    "totalData": 0,
    "totalPage": 1
  }
}
```

## Endpoint Modul

- `/api/users`
- `/api/products`
- `/api/category`
- `/api/sales`
- `/api/sale-details`
- `/api/payments`
- `/api/auth/login`
- `/api/auth/register`
- `/api/auth/me`
- `/api/auth/logout`
