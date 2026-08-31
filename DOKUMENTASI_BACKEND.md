# 🖥️ Dokumentasi Backend & Portal Web (Laravel 13)
### KMI Road System • PT Sanghiang Perkasa (Kalbe Nutritionals)

Dokumen ini berisi panduan teknis lengkap mengenai arsitektur, konfigurasi basis data PostgreSQL, migrasi tabel, daftar endpoint REST API, dan cara menjalankan server backend **KMI Road**.

---

## 🏛️ 1. Arsitektur & Teknologi Backend

- **Framework**: Laravel 13.29.0
- **Bahasa Pemrograman**: PHP 8.4.18 (NTS)
- **Database**: PostgreSQL 15+ / 16+ (Database Name: `KMIROADHC2026`)
- **Autentikasi**:
  - Web: Laravel Stateful Session & Blade Templates
  - Mobile Driver API: Laravel Sanctum Bearer Token
- **Standar Penamaan Database**: Sesuai [KMI_Database_Standard.md](KMI_Database_Standard.md)
- **Desain UI Web**: Kalbe Nutritionals Corporate Design System (Emerald Green `#008542`, Deep Forest `#064E2B`, Eco Lime `#84BD00`)
- **Peta Live Tracking**: Leaflet.js & OpenStreetMap

---

## 📂 2. Struktur Direktori Backend (`kmi-road-backend`)

```
kmi-road-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/               # Controller Portal Admin HC
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── LiveTrackingController.php
│   │   │   │   ├── TripController.php
│   │   │   │   ├── VehicleController.php
│   │   │   │   ├── DriverController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── Api/                 # Controller REST API Driver Mobile App
│   │   │   │   ├── DriverAuthController.php
│   │   │   │   ├── DriverTripController.php
│   │   │   │   ├── DriverTrackingController.php
│   │   │   │   └── DriverDocumentationController.php
│   │   │   └── Employee/            # Controller Form Karyawan Mandiri
│   │   │       └── BookingController.php
│   └── Models/                      # Eloquent Models sesuai standar KMI
│       ├── mUser.php
│       ├── mDriver.php
│       ├── mVehicle.php
│       ├── mDepartment.php
│       ├── trDutyTrip.php
│       ├── trDutyTrip_Details.php
│       ├── trDutyTrip_Documentations.php
│       ├── dtLocationTracking.php
│       └── logTripStatus.php
├── database/
│   ├── migrations/                  # 1 File 1 Table Schema Migration
│   └── seeders/
│       └── DatabaseSeeder.php       # Data Awal Master & Sample Perjalanan
├── resources/views/                 # Blade Templates UI
│   ├── layouts/                     # app.blade.php & admin.blade.php
│   ├── employee/                    # form, success, status
│   └── admin/                       # dashboard, tracking, trips, vehicles, drivers, reports
├── routes/
│   ├── web.php                      # Web Routes Karyawan & Admin
│   └── api.php                      # REST API Routes Driver Mobile App
└── tests/Feature/
    └── KmiRoadApiTest.php           # Automated PHPUnit Feature Tests
```

---

## ⚙️ 3. Panduan Instalasi & Konfigurasi Backend

### A. Prasyarat Sistem
- PHP `>= 8.2.0` (disarankan PHP 8.4) dengan ekstensi `pdo_pgsql`, `pgsql`, `mbstring`, `fileinfo`
- Composer `>= 2.7.0`
- PostgreSQL Server aktif pada port `5432`

### B. Langkah Instalasi

1. **Masuk ke direktori backend**:
   ```bash
   cd "kmi-road-backend"
   ```

2. **Instal paket dependensi PHP**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**:
   Salin `.env.example` ke `.env` jika belum ada:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Pastikan konfigurasi PostgreSQL pada `.env` telah sesuai:
   ```ini
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=KMIROADHC2026
   DB_USERNAME=postgres
   DB_PASSWORD=root
   ```

4. **Buat Database di PostgreSQL**:
   ```sql
   CREATE DATABASE "KMIROADHC2026";
   ```

5. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Buat Symbolic Link Storage (Untuk Foto Dokumentasi)**:
   ```bash
   php artisan storage:link
   ```

---

## 🗄️ 4. Struktur Tabel Migrasi Database (1 File 1 Table)

Tabel-tabel database dipisahkan secara modular:

| No | File Migrasi | Tabel | Fungsi |
|:---:|:---|:---|:---|
| 1 | `2026_08_30_000001_create_mUser_table.php` | `mUser` | Master Akun Admin HC |
| 2 | `2026_08_30_000002_create_mDriver_table.php` | `mDriver` | Master Akun Driver & No SIM |
| 3 | `2026_08_30_000003_create_mVehicle_table.php` | `mVehicle` | Master Mobil, Plat, Kapasitas Kursi (`intMaxSeat`) |
| 4 | `2026_08_30_000004_create_mDepartment_table.php` | `mDepartment` | Master Departemen Kalbe Nutritionals |
| 5 | `2026_08_30_000005_create_trDutyTrip_table.php` | `trDutyTrip` | Header Transaksi Jadwal Perjalanan Dinas |
| 6 | `2026_08_30_000006_create_trDutyTrip_Details_table.php` | `trDutyTrip_Details` | Detail Penumpang & Form Pengajuan Mandiri |
| 7 | `2026_08_30_000007_create_trDutyTrip_Documentations_table.php` | `trDutyTrip_Documentations` | Bukti Foto Checkpoint (Start, BBM, Tiba, Selesai) |
| 8 | `2026_08_30_000008_create_dtLocationTracking_table.php` | `dtLocationTracking` | Telemetry Real-Time GPS Posisi Armada |
| 9 | `2026_08_30_000009_create_logTripStatus_table.php` | `logTripStatus` | Audit Trail Riwayat Status Perjalanan |
| 10 | `2026_08_30_132553_create_personal_access_tokens_table.php` | `personal_access_tokens` | Token Otentikasi Sanctum Driver |

---

## 🌐 5. Daftar Lengkap REST API Endpoints

### A. Endpoint Publik & Karyawan
| Method | Endpoint | Fungsi |
|---|---|---|
| `GET` | `/` | Form pengajuan perjalanan dinas mandiri |
| `POST` | `/submit` | Submit formulir pengajuan dinas baru |
| `GET` | `/api/check-vehicles` | Cek ketersediaan mobil & sisa kursi dinamis berdasarkan tanggal (`?date=YYYY-MM-DD`) |
| `GET` | `/cek-status` | Halaman pelacakan status tiket |
| `POST` | `/cek-status` | Proses pencarian status tiket berdasarkan kode booking |

### B. Endpoint Autentikasi Driver Mobile (`/api/driver/*`)
| Method | Endpoint | Auth | Fungsi |
|---|---|---|---|
| `POST` | `/api/driver/login` | No | Login driver menggunakan `txtEmail` dan `password` |
| `GET` | `/api/driver/profile` | Bearer Token | Mengambil data profil driver login |
| `POST` | `/api/driver/status` | Bearer Token | Mengubah status driver (`AVAILABLE` / `OFF`) |
| `POST` | `/api/driver/logout` | Bearer Token | Logout dan revoke token Sanctum |

### C. Endpoint Operasional Tugas Driver (`/api/driver/*`)
| Method | Endpoint | Auth | Fungsi |
|---|---|---|---|
| `GET` | `/api/driver/trips` | Bearer Token | Mengambil daftar tugas (Active, Upcoming, History) |
| `GET` | `/api/driver/trips/{id}` | Bearer Token | Detail spesifik tugas, armada, dan penumpang |
| `POST` | `/api/driver/trips/{id}/start` | Bearer Token | Memulai tugas & menginput odometer awal |
| `POST` | `/api/driver/trips/{id}/arrived` | Bearer Token | Menandai telah tiba di lokasi tujuan |
| `POST` | `/api/driver/trips/{id}/complete` | Bearer Token | Menyelesaikan tugas & menginput odometer akhir |

### D. Endpoint Live GPS Telemetry & Bukti Foto Checkpoint
| Method | Endpoint | Auth | Parameter | Fungsi |
|---|---|---|---|---|
| `POST` | `/api/driver/location-update` | Bearer Token | `trip_id`, `latitude`, `longitude`, `speed`, `heading` | Ingest koordinat real-time posisi armada |
| `POST` | `/api/driver/trips/{id}/documentation` | Bearer Token | `photo` (multipart), `category`, `odometer`, `fuel_liters`, `fuel_cost`, `latitude`, `longitude` | Unggah foto bukti dokumentasi perjalanan |

---

## 🚀 6. Cara Menjalankan Server Backend

Untuk menjalankan server pengembangan lokal:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
- **Web Portal**: `http://127.0.0.1:8000`
- **Portal Admin HC**: `http://127.0.0.1:8000/admin/login` (User: `admin@kmi.kalbe.co.id` / Pass: `admin123`)

---

## 🧪 7. Menjalankan Automated Test (PHPUnit)

Jalankan test suite untuk memvalidasi seluruh fungsi backend:
```bash
php artisan test
```
*Output: `PASS (3 tests, 48 assertions)`*.
