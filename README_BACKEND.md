# Backend Work Order Infusion Pump

Backend sudah menggunakan Laravel 11 dengan model, migration, seeder, controller, middleware role, service rule engine, dan feature test.

## Akun Demo

Semua password: `password`

```text
perawat@rs.test
teknisi@rs.test
admin@rs.test
superadmin@rs.test
```

## Menjalankan Lokal Dengan MySQL

Default project sekarang memakai MySQL:

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=workorder_v2
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan:

```bat
migrate-mysql.bat
serve-mysql.bat
```

Akses aplikasi:

```text
http://127.0.0.1:8000/login
```

## Opsi SQLite

PHP CLI di mesin ini belum mengaktifkan SQLite secara default, jadi gunakan script berikut:

```bat
migrate-sqlite.bat
serve-sqlite.bat
```

## Testing

```bat
test-mysql.bat
```

Perintah manual yang ekuivalen:

```bat
C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS workorder_v2_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan test
```

## Modul Backend

- Login dan session Laravel
- Middleware role: perawat, teknisi, admin, super admin
- Master ruangan/unit
- Master alat kesehatan
- Master gejala, indikasi, saran perbaikan
- Rule engine berbasis database
- Pembuatan Work Order dari gejala
- Indikasi awal otomatis
- Update status dan timeline WO
- Notifikasi in-app berbasis tabel
- Laporan dan export CSV

Feature test memakai database MySQL terpisah `workorder_v2_test`, jadi database dev `workorder_v2` tidak ikut dihapus.
