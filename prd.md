# PRODUCT REQUIREMENTS DOCUMENT
## Sistem Digital Work Order Berbasis Barcode
### untuk Identifikasi Indikasi Kerusakan dan Saran Perbaikan Awal pada Alat Kesehatan Infusion Pump

| Dokumen | Versi | Status |
|---|---|---|
| PRD-WO-INFUSION-001 | 1.0 | Draft |

| Platform | Framework | Database |
|---|---|---|
| Web-Based | Laravel 11 | MySQL 8 |

---

## 1. RINGKASAN EKSEKUTIF

### 1.1 Latar Belakang

Sistem Work Order manual yang digunakan di rumah sakit saat ini belum mampu memberikan gambaran awal mengenai jenis atau lokasi kerusakan alat kesehatan. Identifikasi masih sepenuhnya bergantung pada pemeriksaan langsung oleh teknisi, sehingga proses penanganan menjadi kurang efisien dan membutuhkan waktu lebih lama.

Sistem digital Work Order berbasis web ini dirancang untuk mengatasi permasalahan tersebut, dengan fokus pada alat kesehatan Infusion Pump sebagai objek penelitian utama. Sistem mengintegrasikan teknologi barcode sebagai identifikasi alat, input gejala oleh pengguna, dan mesin identifikasi berbasis rule-based untuk menghasilkan indikasi awal kerusakan beserta rekomendasi penanganan awal.

### 1.2 Tujuan Sistem

- Mempercepat proses pelaporan kerusakan alat kesehatan Infusion Pump
- Memberikan indikasi awal kerusakan berdasarkan gejala yang dipilih pengguna
- Menyediakan saran perbaikan awal sebagai panduan teknisi sebelum turun ke lapangan
- Mendokumentasikan seluruh riwayat Work Order secara digital dan terstruktur
- Mengidentifikasi alat kesehatan secara akurat menggunakan barcode

### 1.3 Batasan Sistem

Sistem ini **tidak** bertujuan untuk menggantikan diagnosis teknis oleh teknisi elektromedis. Sistem hanya berfungsi sebagai alat bantu identifikasi awal berdasarkan gejala yang dilaporkan pengguna. Objek penelitian dibatasi pada satu jenis alat: Infusion Pump (Syringe Pump).

---

## 2. STACK TEKNOLOGI

| Komponen | Teknologi | Fungsi |
|---|---|---|
| Backend Framework | Laravel 11.x | Logika bisnis, routing, API, ORM |
| Bahasa Pemrograman | PHP 8.2+ | Server-side scripting |
| Database | MySQL 8.0+ | Penyimpanan data relasional |
| Frontend Templating | Blade + Tailwind CSS | Tampilan antarmuka pengguna |
| Barcode Scanner | ZXing.js / Html5-Qrcode | Pemindaian barcode via kamera browser |
| Barcode Generator | Simple QrCode (Laravel) | Generate QR Code untuk label alat |
| Auth & Session | Laravel Sanctum / Breeze | Autentikasi & manajemen sesi |
| Notifikasi | Laravel Notifications | Email / in-app notifikasi teknisi |
| Web Server | Nginx / Apache | Web server |
| PDF Export | DomPDF (barryvdh) | Cetak laporan Work Order |

---

## 3. USER ROLES & STAKEHOLDERS

| Role | Deskripsi | Akses Utama |
|---|---|---|
| Perawat / Pengguna Alat | Staf medis yang mengoperasikan infusion pump dan melaporkan kerusakan | Scan barcode, input gejala, kirim WO |
| Teknisi Elektromedis | Teknisi yang menangani perbaikan alat kesehatan | Lihat WO, update status, catat perbaikan |
| Supervisor / Admin | Kepala IPSRS / Admin sistem rumah sakit | Kelola master data, laporan, user management |
| Super Admin | Developer / IT admin sistem | Full akses semua fitur dan konfigurasi |

---

## 4. FITUR & MODUL SISTEM

### 4.1 Modul Autentikasi
- Login multi-role (Perawat, Teknisi, Admin, Super Admin)
- Manajemen sesi dan keamanan password (bcrypt hashing)
- Middleware proteksi route berdasarkan role
- Fitur lupa password via email

### 4.2 Modul Manajemen Alat Kesehatan
- CRUD data alat kesehatan (nama, model, serial number, lokasi, tahun pengadaan)
- Generate dan cetak QR Code unik per alat
- Tempel label barcode fisik pada alat
- Histori pemeliharaan per alat
- Status alat: Aktif / Dalam Perbaikan / Non-Aktif

### 4.3 Modul Scan Barcode & Input Gejala

Alur utama pelaporan kerusakan oleh Perawat:

1. Perawat membuka sistem via browser smartphone
2. Klik tombol "Laporkan Kerusakan" → aktifkan kamera
3. Scan QR Code yang tertempel pada infusion pump
4. Sistem menampilkan otomatis: Nama alat, Ruangan, Nomor Inventaris
5. Perawat memilih gejala utama dari daftar yang tersedia (checkbox / radio button)
6. Sistem memproses gejala via rule-based engine
7. Tampilkan hasil: Indikasi Awal Kerusakan + Saran Perbaikan Awal
8. Perawat mengisi keterangan tambahan (opsional) dan submit laporan
9. Work Order otomatis dibuat dan notifikasi dikirim ke teknisi

### 4.4 Modul Identifikasi Kerusakan (Rule-Based Engine)

Engine ini adalah inti sistem — mencocokkan gejala yang dipilih dengan database rule untuk menghasilkan indikasi kerusakan. Berdasarkan Flowchart Troubleshoot Infusion Pump (Syringe), terdapat 6 tahap pemeriksaan:

| Step | Pemeriksaan | Gejala Input | Indikasi Kerusakan | Saran Awal |
|---|---|---|---|---|
| 1 | POWER CHECK | Alat tidak menyala | Sistem Power (kabel, adaptor, baterai, fuse, power board, tombol, konektor internal) | Cek sumber daya: kabel, adaptor, baterai, sekering |
| 2 | ALARM CHECK | Ada alarm aktif | Sesuai jenis alarm (occlusion, air bubble, door open, low battery, system error) | Lakukan tindakan sesuai panduan alarm di user manual |
| 3 | PERFORMA CHECK | Cairan tidak keluar / flow rate tidak stabil | Gelembung udara, sumbatan selang, ukuran syringe tidak sesuai, konektor longgar | Bersihkan/ganti komponen dan pastikan setting benar |
| 4 | SENSOR CHECK | Sensor tidak merespons / error sensor | Kerusakan Sensor (tekanan/occlusion, udara/air bubble, posisi plunger) | Bersihkan sensor / periksa kabel konektor sensor |
| 5 | MEKANIK & MOTOR CHECK | Suara aneh / motor tidak bergerak | Motor / Driver / Mekanik (motor, lead screw, mekanisme penggerak, driver motor) | Bersihkan, lumasi, atau perbaiki bagian mekanik/motor |
| 6 | SOFTWARE CHECK | Error tampilan / sistem hang / error kode | Software / System (firmware error, software corrupt) | Update / instal ulang firmware |

**Catatan implementasi:**
- Implementasi menggunakan Laravel Eloquent relasi many-to-many antara tabel `symptoms` dan `damage_indications`
- Setiap rule disimpan di database (tabel `rules`) sehingga dapat dikonfigurasi admin tanpa coding
- Satu laporan dapat menghasilkan beberapa indikasi jika gejala memenuhi multiple rules
- Prioritas indikasi ditentukan oleh bobot/weight yang dapat diatur admin

### 4.5 Modul Work Order Management
- Pembuatan WO otomatis saat perawat submit laporan
- Status WO: Menunggu → Diproses → Selesai → Ditutup
- Teknisi dapat update status, tambah catatan teknis, upload foto hasil perbaikan
- Teknisi dapat override atau tambahkan diagnosis akhir yang berbeda dari indikasi awal
- Riwayat lengkap timeline setiap WO (siapa, kapan, apa yang dilakukan)
- Filter dan pencarian WO berdasarkan tanggal, ruangan, status, alat, teknisi

### 4.6 Modul Notifikasi
- Notifikasi in-app (badge/alert) ke teknisi saat WO baru masuk
- Email notifikasi ke teknisi yang ditugaskan
- Notifikasi ke perawat saat status WO berubah
- Notifikasi ke supervisor jika WO belum diproses melebihi batas waktu yang dikonfigurasi

### 4.7 Modul Dashboard & Laporan

**Dashboard Perawat:**
- Status WO yang pernah dilaporkan
- Tombol cepat "Laporkan Kerusakan"

**Dashboard Teknisi:**
- Daftar WO baru dan yang sedang diproses
- Ringkasan: Total WO hari ini, minggu ini, selesai, pending
- Detail indikasi awal dan saran per WO

**Dashboard Admin:**
- Statistik total WO per periode
- Grafik kerusakan per jenis gejala / indikasi
- Top alat dengan kerusakan terbanyak
- Rata-rata waktu penanganan WO
- Export laporan ke PDF dan Excel

### 4.8 Modul Manajemen Pengguna (Admin)
- CRUD user (nama, email, role, ruangan/unit kerja)
- Assign teknisi ke WO tertentu
- Log aktivitas pengguna

### 4.9 Modul Konfigurasi Sistem (Super Admin)
- Manajemen master data: Jenis Alat, Ruangan, Gejala, Indikasi, Saran Perbaikan
- Konfigurasi rule-based engine (tambah/edit/hapus rules)
- Konfigurasi threshold notifikasi (SLA WO)

---

## 5. DATABASE DESIGN

### 5.1 Daftar Tabel

| Tabel | Deskripsi | Kolom Utama |
|---|---|---|
| `users` | Data pengguna sistem | id, name, email, password, role (enum), unit_id, created_at |
| `units` | Ruangan / unit kerja RS | id, name, floor, building |
| `devices` | Data alat kesehatan | id, name, type, model, serial_number, inventory_number, unit_id, barcode_code, status (enum), purchased_at |
| `symptoms` | Daftar gejala yang bisa dipilih | id, code, name, category (power/alarm/performa/sensor/mekanik/software), description |
| `damage_indications` | Daftar indikasi kerusakan | id, code, name, severity (low/medium/high/critical), description |
| `repair_suggestions` | Saran perbaikan awal | id, damage_indication_id, step_order, action_text |
| `rules` | Rule penghubung gejala → indikasi | id, name, weight, is_active |
| `rule_symptoms` | Pivot: rules ↔ symptoms | id, rule_id, symptom_id |
| `rule_indications` | Pivot: rules ↔ damage_indications | id, rule_id, damage_indication_id |
| `work_orders` | Data Work Order | id, wo_number (unique), device_id, reporter_id, technician_id, status (enum), description, created_at |
| `wo_symptoms` | Gejala yang dipilih pada WO | id, work_order_id, symptom_id |
| `wo_indications` | Indikasi yang dihasilkan sistem | id, work_order_id, damage_indication_id, source (system/manual) |
| `wo_updates` | Timeline/log update WO | id, work_order_id, user_id, status, notes, photo_path, updated_at |
| `notifications` | Notifikasi sistem | id, user_id, type, data (JSON), read_at, created_at |

### 5.2 Relasi Antar Tabel

- `devices` BELONGS TO `units`
- `work_orders` BELONGS TO `devices`, `users` (reporter), `users` (technician)
- `work_orders` HAS MANY `wo_symptoms`, `wo_indications`, `wo_updates`
- `wo_symptoms` BELONGS TO `symptoms`
- `wo_indications` BELONGS TO `damage_indications`
- `damage_indications` HAS MANY `repair_suggestions`
- `rules` HAS MANY THROUGH `rule_symptoms` → `symptoms`
- `rules` HAS MANY THROUGH `rule_indications` → `damage_indications`

---

## 6. ARSITEKTUR SISTEM

| Layer | Komponen | Deskripsi |
|---|---|---|
| Presentation Layer | Blade Templates + Tailwind CSS | Tampilan antarmuka berbasis server-side rendering, responsif untuk mobile dan desktop |
| Application Layer | Laravel Controllers + Services | Logika bisnis, validasi input, orkestrasi rule-based engine, manajemen WO |
| Rule Engine | Laravel Service Class (RuleEngineService) | Memproses gejala yang dipilih, query rules dari DB, return indikasi + saran perbaikan |
| Data Layer | Laravel Eloquent ORM + MySQL | Penyimpanan dan retrieval data relasional |
| Barcode Layer | Simple QrCode + Html5-Qrcode | Generate QR Code per alat dan scan via kamera browser |
| Notification Layer | Laravel Notifications + Mail | Kirim notifikasi in-app dan email ke teknisi/admin |

### 6.1 Struktur Folder Laravel

```
app/Http/Controllers/    → AuthController, WorkOrderController, DeviceController, DashboardController, AdminController
app/Services/            → RuleEngineService, BarcodeService, NotificationService
app/Models/              → User, Device, WorkOrder, Symptom, DamageIndication, Rule, WoUpdate
database/migrations/     → semua tabel
database/seeders/        → UserSeeder, SymptomSeeder, RuleSeeder, DeviceSeeder
resources/views/         → auth, dashboard, workorder, device, admin, report
routes/web.php           → routing terpisah per role via middleware
```

---

## 7. ALUR SISTEM (USER FLOW)

### 7.1 Flow Perawat — Laporkan Kerusakan

| Langkah | Aksi Pengguna | Response Sistem |
|---|---|---|
| 1 | Login dengan akun perawat | Dashboard perawat tampil, lihat WO aktif miliknya |
| 2 | Klik "Laporkan Kerusakan Baru" | Tampil halaman scan barcode dengan akses kamera |
| 3 | Arahkan kamera ke QR Code alat | Sistem decode QR → tampil detail alat otomatis |
| 4 | Konfirmasi data alat, klik "Lanjut" | Tampil form checklist gejala berdasarkan kategori |
| 5 | Pilih satu atau lebih gejala | Sistem preview indikasi sementara (real-time) |
| 6 | Isi catatan tambahan (opsional) | Input tersimpan sementara di form |
| 7 | Klik "Kirim Laporan" | Rule engine berjalan, WO dibuat, nomor WO tampil |
| 8 | Lihat hasil: Indikasi + Saran Perbaikan | Halaman konfirmasi WO dengan indikasi dan saran |
| 9 | Notifikasi terkirim ke teknisi | Teknisi mendapat alert WO baru |

### 7.2 Flow Teknisi — Tangani Work Order

| Langkah | Aksi Teknisi | Response Sistem |
|---|---|---|
| 1 | Terima notifikasi WO baru | Badge notifikasi di dashboard, email alert |
| 2 | Buka detail WO | Tampil: detail alat, ruangan, gejala, indikasi awal, saran, pelapor |
| 3 | Update status → "Sedang Diproses" | Status WO berubah, perawat mendapat notifikasi |
| 4 | Lakukan perbaikan di lapangan | Offline — tidak perlu sistem |
| 5 | Catat hasil perbaikan di sistem | Input catatan teknis, diagnosis akhir, upload foto |
| 6 | Update status → "Selesai" | WO ditutup, timestamp selesai tercatat, perawat notif |
| 7 | Supervisor verifikasi dan tutup WO | Status → "Ditutup", masuk riwayat |

---

## 8. KEBUTUHAN NON-FUNGSIONAL

| Aspek | Requirement | Target |
|---|---|---|
| Performance | Halaman scan barcode harus load cepat | < 3 detik pada koneksi 4G |
| Performance | Rule engine processing time | < 1 detik setelah submit gejala |
| Security | Autentikasi dan otorisasi berbasis role | Middleware pada semua route sensitif |
| Security | Proteksi terhadap SQL Injection dan XSS | Laravel built-in protection + validasi input |
| Security | HTTPS untuk akses production | SSL certificate wajib |
| Usability | Responsif di smartphone (mobile-first) | Tested pada resolusi 375px - 1440px |
| Usability | Proses laporan selesai dalam hitungan menit | Maks. 3 menit untuk perawat tanpa pelatihan khusus |
| Availability | Uptime sistem | 99% uptime pada jam kerja RS |
| Scalability | Mendukung multiple alat dan ruangan | Min. 100 alat, 50 user aktif bersamaan |
| Auditability | Setiap aksi tercatat dalam log | Custom log tabel atau Laravel Activity Log |

---

## 9. ACCEPTANCE CRITERIA

| ID | Kriteria | Pass Condition |
|---|---|---|
| AC-01 | Scan barcode alat berhasil | Sistem menampilkan data alat dalam < 3 detik setelah scan |
| AC-02 | Rule engine menghasilkan indikasi | Minimal 1 indikasi kerusakan tampil setelah gejala dipilih dan di-submit |
| AC-03 | Work Order dibuat otomatis | WO dengan nomor unik muncul di dashboard teknisi setelah laporan dikirim |
| AC-04 | Notifikasi teknisi terkirim | Teknisi menerima notifikasi in-app dan/atau email dalam < 1 menit |
| AC-05 | Update status WO berfungsi | Teknisi dapat mengubah status WO dan perubahan tersimpan dengan timestamp |
| AC-06 | Dashboard laporan akurat | Data statistik WO sesuai dengan jumlah record di database |
| AC-07 | Akses role terlindungi | Perawat tidak dapat mengakses halaman admin/teknisi dan sebaliknya |
| AC-08 | Export laporan berhasil | File PDF/Excel ter-download dan berisi data yang benar |
| AC-09 | Sistem responsif mobile | Semua halaman utama dapat digunakan nyaman di layar 375px |
| AC-10 | Riwayat WO tersimpan | Semua WO yang sudah selesai dapat dilihat di histori dengan detail lengkap |

---

## 10. CATATAN TAMBAHAN

### Rekomendasi Pengembangan Selanjutnya (Future Enhancement)
- Integrasi dengan sistem SIMRS (Sistem Informasi Manajemen RS) yang sudah ada
- Perluasan ke alat kesehatan lain (Infusion Pump Volumetric, Ventilator, dll.)
- Fitur foto kondisi alat saat pelaporan oleh perawat
- Dashboard analytics lebih advanced (prediksi kerusakan berbasis histori)
- Mobile app native (Flutter) sebagai pengganti akses web di smartphone
- Integrasi WhatsApp notification untuk teknisi via Fonnte / WhatsApp Cloud API
- Machine learning untuk meningkatkan akurasi rule engine dari histori kasus nyata

### Catatan Implementasi Penting
- Seed data gejala, indikasi, dan rules harus diisi lengkap sebelum sistem diuji
- QR Code label fisik harus dicetak dan ditempelkan pada setiap unit infusion pump sebelum go-live
- Pelatihan minimal 30 menit untuk perawat dan 1 jam untuk teknisi sebelum go-live
- Backup database harian wajib dikonfigurasi di production server
- Pastikan semua smartphone perawat mendukung akses kamera dari browser (tidak diblokir kebijakan RS)

---

*PRD-WO-INFUSION-001 | v1.0 | Institut Kesehatan dan Teknologi Al Insyirah, Pekanbaru*