# Sales Report Backend

Backend untuk aplikasi `inventory` berbasis Laravel dengan struktur `module-based`.

## Struktur Arsitektur

- `app/Modules/Shared` berisi komponen bersama seperti response, base controller, base repository, base service, dan base resource.
- `app/Modules/{Feature}` berisi fitur per modul, misalnya `User` dan `Product`.
- Setiap modul memiliki `Controllers`, `DTOs`, `Requests`, `Resources`, `Repositories`, `Services`, dan `Routes`.

## Dokumen Terkait

- [Installation Guide](./installation-guide.md)
- [New Module Guide](./new-module-guide.md)
- [Backend Overview](./backend-overview.md)
- [Postman Collection](./postman/inventory-api.postman_collection.json)
- [Postman Environment](./postman/inventory-api.postman_environment.json)
