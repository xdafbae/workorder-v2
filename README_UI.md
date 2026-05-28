# UI Prototype Work Order Infusion Pump

Prototype awal dibuat berdasarkan `prd.md`. Backend Laravel sekarang sudah tersedia; file statis tetap disimpan sebagai referensi UI cepat.

## Akses Cepat Tanpa Laravel

Buka file berikut langsung dari browser:

```text
public/prototype.html
```

Halaman ini memakai Tailwind CDN dan dummy data di JavaScript.

Untuk sistem backend penuh, jalankan:

```bat
migrate-mysql.bat
serve-mysql.bat
```

Lalu buka:

```text
http://127.0.0.1:8000/login
```

## Struktur Blade Laravel

File Blade sudah disiapkan agar mudah dipindahkan ke Laravel penuh:

```text
resources/views/layouts/app.blade.php
resources/views/auth/login.blade.php
resources/views/dashboard/perawat.blade.php
resources/views/dashboard/teknisi.blade.php
resources/views/dashboard/admin.blade.php
resources/views/workorder/create.blade.php
resources/views/workorder/show.blade.php
resources/views/devices/index.blade.php
resources/views/admin/rules.blade.php
resources/views/reports/index.blade.php
routes/web.php
```

## Route Dummy

Jika project Laravel sudah tersedia, route ini bisa dibuka tanpa database:

```text
/login
/dashboard/perawat
/work-orders/create
/dashboard/teknisi
/work-orders/WO-2026-0008
/dashboard/admin
/devices
/admin/rules
/reports
```

Data halaman Laravel sekarang berasal dari database, controller, dan service rule engine.
