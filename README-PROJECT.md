# 🚗 KMI Road - Fleet & Duty Travel Management System
### PT Sanghiang Perkasa • Kalbe Nutritionals

**KMI Road** adalah sistem terintegrasi pemantauan perjalanan dinas karyawan, pengelolaan armada mobil operasional, live tracking GPS posisi kendaraan secara real-time (seperti fitur *Live Location* WhatsApp), serta verifikasi bukti foto dokumentasi perjalanan dinas (*checkpoint*: sebelum dinas, pengisian BBM + struk, tiba di lokasi, dan selesai dinas).

---

## 📑 Daftar Isi Dokumentasi

1. [📘 Panduan Penggunaan Tim HC & Karyawan (User Guide)](PANDUAN_USER_HC.md)
2. [🖥️ Dokumentasi Backend & Portal Web Laravel 13](DOKUMENTASI_BACKEND.md)
3. [📱 Dokumentasi Aplikasi Driver Android Flutter](DOKUMENTASI_DRIVER_APP.md)
4. [🗄️ Standar Database & Naming Convention KMI](KMI_Database_Standard.md)

---

## 🏛️ Arsitektur Sistem

```
                         ┌─────────────────────────────────────────┐
                         │      Karyawan (Tanpa Perlu Login)       │
                         │   • Form Pengajuan Dinas Mandiri        │
                         │   • Dynamic Seat Availability Check     │
                         │   • Tracking Status Tiket Booking       │
                         └────────────────────┬────────────────────┘
                                              │ (HTTP / Web)
                                              ▼
┌─────────────────────────┐       ┌───────────────────────────────┐       ┌─────────────────────────┐
│     Portal Admin HC     │       │   KMI Road Backend API Server │       │    Driver Android App   │
│  (Laravel 13 + Leaflet) │◄─────►│    (Laravel 13 + PostgreSQL)  │◄─────►│     (Flutter 3.41.4)    │
│  • Dashboard Armada     │ (Web) │  • Sanctum Authentication     │ (API) │  • Live GPS Telemetry   │
│  • Dispatcher & Assign  │       │  • Seat Engine & Validation   │       │  • 4 Checkpoint Kamera  │
│  • Live GPS Radar Map   │       │  • Telemetry Ingestion        │       │  • Detail Penumpang     │
│  • Verifikasi Foto BBM  │       │  • Database: KMIROADHC2026    │       │  • Status Driver Toggle │
│  • Rekap Laporan & CSV  │       └───────────────────────────────┘       └─────────────────────────┘
└─────────────────────────┘
```

---

## 💻 Teknologi yang Digunakan

| Komponen | Teknologi | Versi | Peran |
|---|---|---|---|
| **Backend & Web Portal** | Laravel (PHP) | 13.29.0 (PHP 8.4.18) | Core Web Application, Authentication, API Server |
| **Mobile App Driver** | Flutter / Dart | Flutter 3.41.4 (Dart 3.11.1) | Aplikasi Android Driver, GPS Service, Camera Checkpoint |
| **Database** | PostgreSQL | 15+ / 16+ | RDBMS dengan standar penamaan `KMI_Database_Standard` |
| **Live Map Engine** | Leaflet.js / OpenStreetMap | Latest | Peta interaktif Live Tracking Radar armada |
| **Branding & UI** | Kalbe Nutritionals Corporate Theme | Emerald Green `#008542`, Deep Forest `#064E2B`, Eco Lime `#84BD00` |

---

## 📂 Struktur Direktori Project

```
kmi-road-app/
├── README.md                      # Dokumentasi Utama
├── PANDUAN_USER_HC.md             # Panduan Fitur Lengkap untuk Tim HC & Karyawan
├── DOKUMENTASI_INSTALASI.md       # Panduan Instalasi Backend & Flutter Driver App
├── KMI_Database_Standard.md       # Standar Naming Convention Database KMI
│
├── kmi-road-backend/              # Project Laravel 13 Web & REST API
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── Admin/             # Dashboard, Live Tracking, Trips, Vehicles, Drivers, Reports
│   │   │   ├── Api/               # Driver Auth, Driver Trips, GPS Telemetry, Photo Documentation
│   │   │   └── Employee/          # Form Booking Mandiri & Cek Status Tiket
│   │   └── Models/                # Eloquent Models (mUser, mDriver, mVehicle, trDutyTrip, dll.)
│   ├── database/
│   │   ├── migrations/            # 1 File 1 Table Schema Migration PostgreSQL
│   │   └── seeders/               # Master Data, Akun Default, Sample Trips & Telemetry
│   ├── resources/views/           # Blade Templates UI (Employee & Admin HC Portal)
│   ├── routes/                    # Web & API Route Definitions
│   └── tests/Feature/             # Automated PHPUnit Feature Tests (48 Assertions Passed)
│
└── kmi-road-driver/               # Project Flutter 3.41.4 Android Driver App
    ├── lib/
    │   ├── core/                  # Color Theme Kalbe & Constants
    │   ├── models/                # Driver, Trip, Vehicle, Passenger, Checkpoint Models
    │   ├── providers/             # State Management (AuthProvider, TripProvider)
    │   ├── screens/               # Splash, Login, Home Radar, Active Driving Map, Camera Upload, Profil
    │   └── services/              # HTTP API Service & GPS Location Tracking Service
    └── test/                      # Flutter Unit & Widget Smoke Tests (Passed)
```

---

## 🔑 Kredensial Default Siap Pakai

### 1. Portal Web Admin HC
- **URL**: `http://127.0.0.1:8000/admin/login`
- **Email**: `admin@kmi.kalbe.co.id`
- **Password**: `admin123`

### 2. Aplikasi Driver (Flutter Android)
- **Driver 1**: `joko.santoso@kmi.kalbe.co.id` | **Password**: `driver123`
- **Driver 2**: `bambang.pamungkas@kmi.kalbe.co.id` | **Password**: `driver123`
- **Driver 3**: `asep.sunandar@kmi.kalbe.co.id` | **Password**: `driver123`

---

## ⚡ Quick Start (Menjalankan Cepat)

### 1. Jalankan Backend Web (Laravel 13)
```bash
cd kmi-road-backend
php artisan serve --port=8000
```
Akses web di browser: **`http://127.0.0.1:8000`**

### 2. Jalankan Aplikasi Driver (Flutter)
```bash
cd kmi-road-driver
flutter run
```
