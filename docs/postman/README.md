# Postman Collection

Folder ini berisi dokumentasi API dalam format yang siap di-import ke Postman.

## File

- `inventory-api.postman_collection.json`
- `inventory-api.postman_environment.json`

## Cara Pakai

1. Import `inventory-api.postman_collection.json` ke Postman.
2. Import `inventory-api.postman_environment.json`.
3. Pilih environment `Inventory API Local`.
4. Isi nilai `baseUrl` dan `token` jika perlu.

## Catatan

- Semua endpoint API memakai prefix `/api`.
- Endpoint yang protected memakai header `Authorization: Bearer {{token}}`.
- Endpoint export mengembalikan file CSV.
- Endpoint import product memakai `multipart/form-data` dengan field `file`.
