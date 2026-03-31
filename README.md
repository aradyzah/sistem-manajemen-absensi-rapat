# 📋 Sistem Manajemen Absensi Rapat - Universitas Hasanuddin

Sistem manajemen absensi rapat berbasis Laravel Filament untuk mempermudah pencatatan, pengelolaan, dan pelaporan kehadiran rapat secara digital di lingkungan Universitas Hasanuddin.

## 🚀 Fitur Utama

- **Manajemen Rapat:** Pengelolaan agenda, waktu, dan lokasi rapat (Luring, Daring, Hybrid).
- **Absensi Digital:** Pengisian kehadiran melalui link UUID unik dan QR Code.
- **Validasi Lokasi (Fitur 1):** Verifikasi kehadiran Offline menggunakan IP Address kampus dan koordinat GPS.
- **Laporan PDF:** Export daftar hadir rapat ke format PDF yang siap cetak.
- **Manajemen Unit Kerja:** Pengelompokan user dan rapat berdasarkan unit kerja masing-masing.

## 🛠️ Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Admin Panel:** Filament PHP v3
- **Database:** MySQL
- **Frontend:** Tailwind CSS, Alpine.js (via Filament & Blade)
- **Validation:** Lokasi GPS & IP Address Service

## 📂 Dokumentasi Project

Semua detail teknis dan panduan pengembangan telah dipindahkan ke folder `docs/` agar lebih rapi:

1. [**Dokumentasi Lengkap (README_DOKUMENTASI.md)**](docs/README_DOKUMENTASI.md): Berisi skema database, struktur folder, dan detail model/controller.
2. [**Panduan Fitur 1 (Hybrid Presensi)**](docs/PROMPT_FITUR1_HYBRID_PRESENSI_BACKEND.md): Instruksi detail implementasi backend untuk modul presensi hybrid.
3. [**Handoff Frontend Fitur 1**](docs/HANDOFF_TO_FRONTEND_FITUR1.md): Panduan integrasi API untuk pengembang frontend.

## ⚡ Quick Start

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL

### Installation

1. Clone repositori ini.
2. Jalankan instalasi dependensi:
   ```bash
   composer install
   npm install
   ```
3. Salin file `.env.example` ke `.env` dan sesuaikan konfigurasi database:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```
5. Jalankan development server:
   ```bash
   php artisan serve
   ```

---
© 2026 Universitas Hasanuddin
