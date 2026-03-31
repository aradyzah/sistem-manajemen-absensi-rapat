# 📋 Dokumentasi Lengkap: Sistem Manajemen Absensi Rapat

## 🎯 Deskripsi Project

**Sistem Manajemen Absensi Rapat** adalah aplikasi Laravel berbasis Filament Admin Panel yang dirancang untuk mengelola rapat, peserta rapat, dan pencatatan kehadiran secara digital. Sistem ini memungkinkan pembuatan rapat, QR code absensi, dan export laporan kehadiran dalam format PDF.

**Timezone:** Asia/Makassar (WITA)  
**Database:** MySQL (presensirapat)  
**Environment:** Development (localhost)

---

## 🛠️ Tech Stack & Versi

### Backend (PHP / Laravel)
| Teknologi | Versi |
|-----------|-------|
| **PHP** | ^8.2 |
| **Laravel Framework** | ^12.0 |
| **Laravel Tinker** | ^2.10.1 |
| **Laravel Sail** | ^1.41 |
| **Laravel Pail** | ^1.2.2 |
| **Laravel Pint** | ^1.13 |

### Admin Panel & UI
| Teknologi | Versi | Deskripsi |
|-----------|-------|----------|
| **Filament** | ^3.3 | Admin Panel Framework |
| **Blade UI Kit Heroicons** | ^2.6 | Icon Components |

### Utilities & Libraries
| Teknologi | Versi | Deskripsi |
|-----------|-------|----------|
| **Barry vDh Laravel DOMPDF** | ^3.1 | PDF Generation |
| **Simple QR Code** | ^4.2 | QR Code Generation |
| **Predis** | ^3.0 | Redis Client |
| **Faker PHP** | ^1.23 | Data Seeding |
| **MockeryPHP** | ^1.6 | Mocking Library |
| **PHPUnit** | ^11.5.3 | Unit Testing |
| **Collision** | ^8.6 | Error Display |

### Frontend (Node.js)
| Teknologi | Versi | Deskripsi |
|-----------|-------|----------|
| **Vite** | ^6.2.4 | Build Tool |
| **Tailwind CSS** | ^4.0.0 | Utility CSS |
| **Tailwind CSS Vite** | ^4.0.0 | Vite Plugin |
| **Laravel Vite Plugin** | ^1.2.0 | Vite Plugin untuk Laravel |
| **Axios** | ^1.8.2 | HTTP Client |
| **Concurrently** | ^9.0.1 | Multi-process Runner |

---

## 📁 Struktur Folder Lengkap

```
sistem-manajemen-absensi-rapat/
│
├── 📄 artisan                          # Laravel CLI
├── 📄 composer.json                    # PHP Dependencies
├── 📄 composer.lock
├── 📄 package.json                     # Node Dependencies
├── 📄 package-lock.json
├── 📄 vite.config.js                   # Vite Build Configuration
├── 📄 phpunit.xml                      # PHPUnit Configuration
├── 📄 README.md
├── 📄 .env                             # Environment Variables (Local)
├── 📄 .env.example                     # Environment Template
├── 📄 .gitignore
├── 📄 .gitattributes
├── 📄 .editorconfig
│
├── 📁 app/                             # Aplikasi Core
│   ├── 📁 Filament/                    # Filament Admin Panel
│   │   ├── 📁 Pages/                   # Custom Pages
│   │   ├── 📁 Resources/               # Resource Forms & Tables
│   │   │   ├── 📁 KehadiranRapatResource/
│   │   │   ├── 📁 RapatResource/
│   │   │   ├── 📁 UserResource/
│   │   │   ├── 📄 RapatResource.php
│   │   │   └── 📄 UserResource.php
│   │   └── 📁 Widgets/                 # Dashboard Widgets
│   │
│   ├── 📁 Http/                        # HTTP Layer
│   │   ├── 📄 Kernel.php               # HTTP Middleware
│   │   ├── 📁 Controllers/
│   │   │   ├── 📄 AbsensiController.php
│   │   │   ├── 📄 RapatController.php
│   │   │   ├── 📄 TodayMeetingsController.php
│   │   │   └── 📄 Controller.php
│   │   └── 📁 Middleware/
│   │
│   ├── 📁 Models/                      # Eloquent Models
│   │   ├── 📄 User.php                 # User Model (Admin/SuperAdmin)
│   │   ├── 📄 Rapat.php                # Meeting Model
│   │   ├── 📄 KehadiranRapat.php        # Attendance Record
│   │   └── 📄 UnitKerja.php             # Work Unit/Department
│   │
│   ├── 📁 Providers/
│   │   ├── 📄 AppServiceProvider.php    # Main Service Provider
│   │   ├── 📄 AuthServiceProvider.php   # Authentication Provider
│   │   ├── 📄 FilamentServiceProvider.php
│   │   └── 📁 Filament/
│   │
│   └── 📄 Exceptions/
│
├── 📁 bootstrap/                       # Bootstrap Files
│   ├── 📄 app.php
│   ├── 📄 providers.php
│   └── 📁 cache/
│       ├── 📄 packages.php
│       └── 📄 services.php
│
├── 📁 config/                          # Configuration Files
│   ├── 📄 app.php                      # App Configuration
│   ├── 📄 auth.php                     # Authentication
│   ├── 📄 cache.php                    # Cache Configuration
│   ├── 📄 database.php                 # Database Configuration
│   ├── 📄 filament.php                 # Filament Configuration
│   ├── 📄 filesystems.php
│   ├── 📄 logging.php
│   ├── 📄 mail.php
│   ├── 📄 queue.php
│   ├── 📄 services.php
│   └── 📄 session.php
│
├── 📁 database/
│   ├── 📁 factories/
│   │   └── 📄 UserFactory.php
│   ├── 📁 migrations/                  # Database Migrations
│   │   ├── 📄 0001_01_01_000000_create_users_table.php
│   │   ├── 📄 0001_01_01_000001_create_cache_table.php
│   │   ├── 📄 0001_01_01_000002_create_jobs_table.php
│   │   ├── 📄 2025_05_06_143956_create_rapats_table.php
│   │   ├── 📄 2025_05_06_144858_create_kehadiran_rapats_table.php
│   │   ├── 📄 2025_05_08_143035_create_sessions_table.php
│   │   ├── 📄 2025_05_15_101129_add_waktu_to_rapats_table.php
│   │   ├── 📄 2025_06_04_113516_add_jenis_rapat_lokasi_link_to_rapats_table.php
│   │   ├── 📄 2025_06_09_203755_create_unit_kerjas_table.php
│   │   ├── 📄 2025_06_09_203831_add_unit_kerja_id_to_users_table.php
│   │   ├── 📄 2025_06_09_203924_add_unit_kerja_id_and_user_id_to_rapats_table.php
│   │   ├── 📄 2025_06_09_204623_add_penandatangan_fields_to_rapats_table.php
│   │   ├── 📄 2025_06_09_213120_alter_rapats_table_make_unit_kerja_id_nullable.php
│   │   ├── 📄 2025_06_09_213529_alter_rapats_table_make_user_id_nullable.php
│   │   ├── 📄 2025_06_10_012204_make_nip_nik_nullable_in_kehadiran_rapat_table.php
│   │   ├── 📄 2025_06_10_121356_make_foreign_keys_nullable_in_rapats.php
│   │   └── 📄 2025_06_09_234736_add_status_fields_to_kehadiran_rapat_table.php
│   └── 📁 seeders/
│       ├── 📄 DatabaseSeeder.php
│       └── 📄 SuperAdminSeeder.php
│
├── 📁 public/                          # Publicly Accessible Files
│   ├── 📄 index.php                    # Entry Point
│   ├── 📄 robots.txt
│   ├── 📄 hot                          # Vite Hot Module Reload
│   ├── 📁 css/
│   │   └── 📁 filament/
│   └── 📁 js/
│       └── 📁 logo/
│
├── 📁 resources/                       # Frontend Assets
│   ├── 📁 css/
│   ├── 📁 js/
│   └── 📁 views/                       # Blade Templates
│       └── 📄 welcome.blade.php        # Landing/Home Page
│
├── 📁 routes/                          # Route Definitions
│   ├── 📄 web.php                      # Web Routes
│   └── 📄 console.php                  # Console Commands
│
├── 📁 storage/                         # Storage Files
│   ├── 📁 app/                         # User Uploads
│   ├── 📁 framework/
│   │   ├── 📁 cache/
│   │   ├── 📁 sessions/
│   │   ├── 📁 views/
│   │   └── 📁 testing/
│   └── 📁 logs/                        # Application Logs
│
├── 📁 tests/                           # Test Suite
│   ├── 📄 TestCase.php
│   ├── 📁 Feature/
│   └── 📁 Unit/
│
├── 📁 vendor/                          # Composer Dependencies
│   └── [Many package directories]
│
└── 📁 node_modules/                    # NPM Dependencies
    └── [Many package directories]
```

---

## 🗄️ Database Schema

### 📊 Tables dan Relationships

```
┌─────────────────────┐
│      USERS          │
├─────────────────────┤
│ id (PK)             │
│ name                │
│ email               │
│ password            │
│ role                │ (admin/superadmin)
│ unit_kerja_id (FK)  │
│ remember_token      │
│ email_verified_at   │
│ created_at          │
│ updated_at          │
└─────────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────────┐
│     UNIT_KERJAS     │
├─────────────────────┤
│ id (PK)             │
│ nama                │
│ created_at          │
│ updated_at          │
└─────────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────────────────────┐
│           RAPATS                │
├─────────────────────────────────┤
│ id (PK)                         │
│ noDokumen_rapat                 │
│ noRevisi_rapat                  │
│ tgl_berlaku_rapat               │
│ agenda_rapat                    │
│ hari_rapat                      │
│ tanggal_rapat                   │
│ waktu_mulai                     │
│ waktu_selesai                   │
│ jenis_rapat                     │
│ lokasi_rapat                    │
│ link_meeting                    │
│ link_absensi (UUID)             │
│ penandatangan_jabatan           │
│ penandatangan_nama              │
│ penandatangan_nip               │
│ user_id (FK) - nullable         │
│ unit_kerja_id (FK) - nullable   │
│ created_at                      │
│ updated_at                      │
└─────────────────────────────────┘
        │
        │ 1:N
        ▼
┌────────────────────────────┐
│  KEHADIRAN_RAPATS           │
├────────────────────────────┤
│ id (PK)                    │
│ nama                       │
│ nip_nik - nullable         │
│ unit_kerja                 │
│ jabatan_tugas              │
│ instansi                   │
│ email                      │
│ no_telepon                 │
│ tanda_tangan               │
│ status                     │
│ rapat_id (FK)              │
│ created_at                 │
│ updated_at                 │
└────────────────────────────┘
```

### Core Tables Detail

#### **Users Table**
```sql
CREATE TABLE users (
  id bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  email varchar(255) UNIQUE NOT NULL,
  email_verified_at timestamp NULL,
  password varchar(255) NOT NULL,
  role varchar(50) NOT NULL,              -- admin, superadmin
  unit_kerja_id bigint UNSIGNED NULL,
  remember_token varchar(100) NULL,
  created_at timestamp NULL,
  updated_at timestamp NULL,
  FOREIGN KEY (unit_kerja_id) REFERENCES unit_kerjas(id)
);
```

#### **Rapats Table**
```sql
CREATE TABLE rapats (
  id bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  noDokumen_rapat varchar(255) NULL,
  noRevisi_rapat varchar(255) NULL,
  tgl_berlaku_rapat date NULL,
  agenda_rapat text NULL,
  hari_rapat varchar(255) NULL,
  tanggal_rapat date NOT NULL,
  waktu_mulai time NOT NULL,
  waktu_selesai time NULL,
  jenis_rapat varchar(255) NULL,
  lokasi_rapat varchar(255) NULL,
  link_meeting varchar(255) NULL,
  link_absensi char(36) UNIQUE,            -- UUID
  penandatangan_jabatan varchar(255) NULL,
  penandatangan_nama varchar(255) NULL,
  penandatangan_nip varchar(255) NULL,
  user_id bigint UNSIGNED NULL,
  unit_kerja_id bigint UNSIGNED NULL,
  created_at timestamp NULL,
  updated_at timestamp NULL,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (unit_kerja_id) REFERENCES unit_kerjas(id)
);
```

#### **KehadiranRapats Table**
```sql
CREATE TABLE kehadiran_rapats (
  id bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  nama varchar(255) NOT NULL,
  nip_nik varchar(255) NULL,
  unit_kerja varchar(255) NOT NULL,
  jabatan_tugas varchar(255) NOT NULL,
  instansi varchar(255) NULL,
  email varchar(255) NULL,
  no_telepon varchar(255) NULL,
  tanda_tangan varchar(255) NULL,
  status varchar(255) NULL,
  rapat_id bigint UNSIGNED NOT NULL,
  created_at timestamp NULL,
  updated_at timestamp NULL,
  FOREIGN KEY (rapat_id) REFERENCES rapats(id) ON DELETE CASCADE
);
```

#### **UnitKerjas Table**
```sql
CREATE TABLE unit_kerjas (
  id bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  nama varchar(255) NOT NULL,
  created_at timestamp NULL,
  updated_at timestamp NULL
);
```

---

## 🎭 Models & Relationships

### **User Model** (`app/Models/User.php`)
```php
Class User extends Authenticatable implements FilamentUser {
  // Attributes
  - name: string
  - email: string (unique)
  - password: hashed
  - role: enum (admin, superadmin)
  - unit_kerja_id: foreign key
  
  // Relationships
  - unitKerja() → BelongsTo UnitKerja
  - rapats() → HasMany Rapat
  
  // Methods
  - canAccessPanel(Panel) → bool
}
```

### **Rapat Model** (`app/Models/Rapat.php`)
```php
Class Rapat extends Model {
  // Attributes
  - noDokumen_rapat: string
  - noRevisi_rapat: string
  - tgl_berlaku_rapat: date
  - agenda_rapat: text
  - hari_rapat: string (auto-generated from tanggal_rapat)
  - tanggal_rapat: date
  - waktu_mulai: time
  - waktu_selesai: time
  - jenis_rapat: string
  - lokasi_rapat: string
  - link_meeting: string
  - link_absensi: uuid (auto-generated)
  - penandatangan_jabatan: string
  - penandatangan_nama: string
  - penandatangan_nip: string
  - user_id: foreign key (nullable)
  - unit_kerja_id: foreign key (nullable)
  
  // Relationships
  - kehadirans() → HasMany KehadiranRapat
  - user() → BelongsTo User
  - unitKerja() → BelongsTo UnitKerja
  
  // Lifecycle Hooks
  - booted() → Auto-generate link_absensi UUID
  - booted() → Auto-set hari_rapat on creating/updating
}
```

### **KehadiranRapat Model** (`app/Models/KehadiranRapat.php`)
```php
Class KehadiranRapat extends Model {
  protected $table = 'kehadiran_rapat'
  
  // Attributes
  - nama: string
  - nip_nik: string (nullable)
  - unit_kerja: string
  - jabatan_tugas: string
  - instansi: string (nullable)
  - email: string (nullable)
  - no_telepon: string (nullable)
  - tanda_tangan: string (nullable)
  - status: string (nullable)
  - rapat_id: foreign key
  
  // Relationships
  - rapat() → BelongsTo Rapat
}
```

### **UnitKerja Model** (`app/Models/UnitKerja.php`)
```php
Class UnitKerja extends Model {
  // Attributes
  - nama: string
  
  // Relationships
  - users() → HasMany User
  - rapats() → HasMany Rapat
}
```

---

## 🔌 Controllers

### **AbsensiController** (`app/Http/Controllers/AbsensiController.php`)
Menangani logika absensi peserta rapat:
- `showForm(uuid)` - Tampilkan form absensi berdasarkan link_absensi
- `submitForm(uuid)` - Proses pengiriman form absensi
- `getPesertaByNip(uuid)` - Auto-complete pencarian peserta berdasarkan NIP

### **RapatController** (`app/Http/Controllers/RapatController.php`)
Mengelola data rapat:
- `showAbsensi(id)` - Tampilkan daftar kehadiran rapat

### **TodayMeetingsController** (`app/Http/Controllers/TodayMeetingsController.php`)
Menampilkan rapat hari ini

### **Controller (Base)** (`app/Http/Controllers/Controller.php`)
Base controller untuk semua controller lainnya

---

## 🛣️ Routes

### **Web Routes** (`routes/web.php`)

| Method | Path | Controller | Deskripsi |
|--------|------|-----------|-----------|
| GET | `/` | - Closure | Landing page, tampilkan rapat hari ini |
| GET | `/absensi/{uuid}` | AbsensiController@showForm | Form absensi |
| POST | `/absensi/{uuid}` | AbsensiController@submitForm | Submit absensi |
| GET | `/absensi/{uuid}/cek-nip` | AbsensiController@getPesertaByNip | Auto-complete NIP |
| GET | `/rapat/{id}/absensi` | RapatController@showAbsensi | Daftar kehadiran rapat |
| GET | `/admin/rapats/{rapat}/kehadiran/export` | - Closure | Export PDF kehadiran |
| GET | `/rapat/{rapat}/export-kehadiran` | - Closure | Export PDF kehadiran (alternative) |

---

## 🎨 Filament Admin Panel

### Struktur Filament
```
app/Filament/
├── Pages/              # Custom admin pages
├── Resources/          # CRUD resources
│   ├── KehadiranRapatResource/
│   ├── RapatResource/
│   ├── UserResource/
│   ├── RapatResource.php
│   └── UserResource.php
└── Widgets/            # Dashboard widgets
```

### Resources
- **RapatResource** - CRUD untuk manajemen rapat
- **UserResource** - CRUD untuk manajemen user
- **KehadiranRapatResource** - CRUD untuk kehadiran rapat

---

## 🔐 Environment Configuration

### `.env` File (Development)
```dotenv
# Application
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:46ZTb0F76rm04G6Zmxd6sDf90KLMoOhkLEadpf/BDog=
APP_DEBUG=true
APP_URL=http://localhost

# Localization
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_TIMEZONE=Asia/Makassar

# Database (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presensirapat
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

# Redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# BCrypt
BCRYPT_ROUNDS=12

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

---

## 🗂️ Key Configuration Files

### **config/app.php**
- App name, timezone (Asia/Makassar), locale
- Security cipher (AES-256-CBC)
- Providers & aliases

### **config/database.php**
- Default connection: MySQL
- Connection details dari `.env`

### **config/filament.php**
- Filament admin panel configuration

### **config/auth.php**
- Authentication guards & providers
- Password reset configuration

### **config/cache.php**
- Cache driver configuration

---

## 📋 Database Migrations (Timeline)

| Tanggal | Migration | Tujuan |
|---------|-----------|--------|
| 2025-01-01 | create_users_table | Create users table |
| 2025-01-01 | create_cache_table | Create cache table |
| 2025-01-01 | create_jobs_table | Create jobs table |
| 2025-05-06 | create_rapats_table | Create rapats table |
| 2025-05-06 | create_kehadiran_rapats_table | Create kehadiran_rapats table |
| 2025-05-08 | create_sessions_table | Create sessions table |
| 2025-05-15 | add_waktu_to_rapats_table | Add waktu_mulai, waktu_selesai |
| 2025-06-04 | add_jenis_rapat_lokasi_link_to_rapats_table | Add jenis_rapat, lokasi_rapat, link_meeting |
| 2025-06-09 | create_unit_kerjas_table | Create unit_kerjas table |
| 2025-06-09 | add_unit_kerja_id_to_users_table | Add unit_kerja_id to users |
| 2025-06-09 | add_unit_kerja_id_and_user_id_to_rapats_table | Add foreign keys to rapats |
| 2025-06-09 | add_penandatangan_fields_to_rapats_table | Add penandatangan fields |
| 2025-06-09 | alter_rapats_table_make_unit_kerja_id_nullable | Make unit_kerja_id nullable |
| 2025-06-09 | alter_rapats_table_make_user_id_nullable | Make user_id nullable |
| 2025-06-10 | add_status_fields_to_kehadiran_rapat_table | Add status field |
| 2025-06-10 | make_nip_nik_nullable_in_kehadiran_rapat_table | Make nip_nik nullable |
| 2025-06-10 | make_foreign_keys_nullable_in_rapats | Make foreign keys nullable |

---

## 🚀 Scripts & Commands

### **Composer Scripts** (`composer.json`)

```bash
# Development
composer dev
# Runs: php artisan serve, queue:listen, pail, npm run dev (concurrently)

# Testing
composer test
# Runs config:clear & phpunit

# Post-install
php artisan package:discover --ansi
php artisan filament:upgrade
```

### **NPM Scripts** (`package.json`)

```bash
# Development
npm run dev         # Vite dev server

# Production Build
npm run build       # Vite production build
```

### **Artisan Commands** (Common)

```bash
php artisan migrate                    # Run migrations
php artisan db:seed                    # Run seeders
php artisan make:filament-user         # Create Filament user
php artisan serve                      # Start dev server
php artisan queue:listen               # Start queue worker
php artisan pail                       # Real-time logs
```

---

## 📚 Key Features

### ✅ Implemented
1. **User Management** - Admin & SuperAdmin roles
2. **Rapat Management** - Create, edit, delete meetings
3. **Attendance System** - Digital attendance via unique UUID link
4. **QR Code** - Generate QR codes for attendance
5. **PDF Export** - Export attendance reports to PDF
6. **Unit Kerja** - Organize users by departments
7. **Filament Admin** - Modern admin panel
8. **Real-time Updates** - Database session storage

### 🔧 Available Libraries
- **DOMPDF** - PDF generation
- **Simple QR Code** - QR code generation
- **Blade Heroicons** - UI icons
- **Predis** - Redis caching
- **Laravel Tinker** - REPL for debugging

---

## 📝 Testing

**Test Framework:** PHPUnit ^11.5.3

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/YourTest.php
```

Test locations:
- `tests/Feature/` - Feature tests
- `tests/Unit/` - Unit tests

---

## 🎯 Kesimpulan

Project ini adalah aplikasi **Laravel Admin Panel** lengkap dengan fitur:
- ✅ Multi-user authentication dengan roles
- ✅ Database terstruktur dengan relationships yang jelas
- ✅ Filament Admin Panel untuk management
- ✅ Dynamic attendance system dengan UUID
- ✅ PDF export capabilities
- ✅ Modern frontend dengan Tailwind CSS & Vite

Semua file, folder, dan konfigurasi sudah terdefinisi dengan baik dan siap untuk dikembangkan lebih lanjut atau di-share ke AI agent lain dengan konteks lengkap.

---

**Generated:** 2026-02-19  
**Project:** Sistem Manajemen Absensi Rapat  
**Path:** `d:\webdev\sistem-manajemen-absensi-rapat`
