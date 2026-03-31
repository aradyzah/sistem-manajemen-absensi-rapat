# 🤖 Prompt untuk Gemini MCP — Fitur 1: Modul Presensi Hybrid (Backend Only)

> **Konteks:** Dokumen ini adalah instruksi lengkap untuk mengimplementasikan backend Fitur 1 (Modul Presensi Hybrid) pada project Laravel yang sudah berjalan. Ikuti setiap langkah secara berurutan dan jangan skip bagian manapun.

---

## 📌 Identitas Project

- **Nama Project:** Sistem Manajemen Absensi Rapat
- **Backend Developer:** Irgi
- **Frontend Developer:** Zahrah (mengerjakan komponen `HybridToggle` secara terpisah)
- **Peran kamu sekarang:** Backend — kamu HANYA mengerjakan sisi server (Laravel), tidak menyentuh komponen Vue/JS milik Zahrah
- **Stack:** PHP ^8.2, Laravel ^12.0, Filament ^3.3, MySQL, Laravel Queue (database driver)
- **Timezone:** Asia/Makassar (WITA)
- **Project path:** `d:\webdev\sistem-manajemen-absensi-rapat`

---

## 🗄️ Skema Database Saat Ini (EXISTING — Jangan Diubah)

### Tabel `kehadiran_rapats` (existing):
```sql
id, nama, nip_nik (nullable), unit_kerja, jabatan_tugas,
instansi (nullable), email (nullable), no_telepon (nullable),
tanda_tangan (nullable), status (nullable),
rapat_id (FK → rapats.id ON DELETE CASCADE),
created_at, updated_at
```

### Tabel `rapats` (existing, relevan):
```sql
id, jenis_rapat, lokasi_rapat, link_meeting, link_absensi (UUID),
tanggal_rapat, waktu_mulai, waktu_selesai, ...
```

> ⚠️ **PENTING:** Kolom `jenis_rapat` pada tabel `rapats` sudah ada dan berisi nilai seperti `'Luring'` / `'Daring'` / `'Hybrid'`. Gunakan nilai ini sebagai acuan logika validasi.

---

## 🎯 Tujuan Fitur 1

Menambahkan kemampuan sistem untuk:
1. Mencatat **metode kehadiran peserta** (Online / Offline) per record kehadiran
2. Menyimpan **data bukti lokasi** (IP address dan/atau koordinat GPS) saat peserta melakukan absensi
3. Memvalidasi secara server-side apakah peserta yang mengklaim hadir **Luring (Offline)** benar-benar berada di jaringan/lokasi kampus
4. Menyediakan **API endpoint** yang siap dikonsumsi oleh komponen `HybridToggle` yang dibuat Zahrah

---

## 🛠️ Langkah Implementasi

### LANGKAH 1 — Buat Migration Baru

Buat migration untuk menambahkan kolom pada tabel `kehadiran_rapats`. **Jangan ubah migration lama.**

```bash
php artisan make:migration add_hybrid_fields_to_kehadiran_rapats_table --table=kehadiran_rapats
```

Isi migration:
```php
public function up(): void
{
    Schema::table('kehadiran_rapats', function (Blueprint $table) {
        // Metode kehadiran yang dipilih peserta
        $table->enum('metode_kehadiran', ['Online', 'Offline'])
              ->nullable()
              ->after('status')
              ->comment('Metode kehadiran: Online (Daring) atau Offline (Luring)');

        // IP Address peserta saat submit form absensi
        $table->string('ip_address', 45)
              ->nullable()
              ->after('metode_kehadiran')
              ->comment('IP address peserta saat absensi');

        // Koordinat GPS (format: "lat,lng") — opsional, dikirim dari frontend
        $table->string('location_data', 100)
              ->nullable()
              ->after('ip_address')
              ->comment('Koordinat GPS peserta: format lat,lng');

        // Flag hasil validasi lokasi oleh server
        $table->boolean('is_lokasi_valid')
              ->nullable()
              ->after('location_data')
              ->comment('Hasil validasi lokasi oleh server (null = belum divalidasi)');

        // Catatan hasil validasi untuk keperluan audit/debug
        $table->string('catatan_validasi', 255)
              ->nullable()
              ->after('is_lokasi_valid')
              ->comment('Keterangan hasil validasi lokasi');
    });
}

public function down(): void
{
    Schema::table('kehadiran_rapats', function (Blueprint $table) {
        $table->dropColumn([
            'metode_kehadiran',
            'ip_address',
            'location_data',
            'is_lokasi_valid',
            'catatan_validasi',
        ]);
    });
}
```

Jalankan: `php artisan migrate`

---

### LANGKAH 2 — Update Model `KehadiranRapat`

File: `app/Models/KehadiranRapat.php`

Tambahkan kolom baru ke `$fillable` dan tambahkan casting:

```php
protected $fillable = [
    'nama',
    'nip_nik',
    'unit_kerja',
    'jabatan_tugas',
    'instansi',
    'email',
    'no_telepon',
    'tanda_tangan',
    'status',
    'rapat_id',
    // === KOLOM BARU FITUR 1 ===
    'metode_kehadiran',
    'ip_address',
    'location_data',
    'is_lokasi_valid',
    'catatan_validasi',
];

protected $casts = [
    'is_lokasi_valid' => 'boolean',
];
```

---

### LANGKAH 3 — Buat Service `LokasiValidasiService`

Buat file baru: `app/Services/LokasiValidasiService.php`

Service ini menangani semua logika validasi lokasi secara terpusat agar mudah diubah tanpa menyentuh controller.

```php
<?php

namespace App\Services;

class LokasiValidasiService
{
    /**
     * Daftar range IP kampus UNHAS.
     * Sesuaikan dengan range IP resmi kampus.
     * Format CIDR: ['10.0.0.0/8', '192.168.1.0/24', ...]
     */
    private array $campusIpRanges = [
        '10.0.0.0/8',       // Contoh — ganti dengan IP UNHAS yang sebenarnya
        '192.168.0.0/16',   // Contoh — ganti dengan IP internal UNHAS
        // Tambahkan range IP resmi UNHAS di sini
    ];

    /**
     * Koordinat pusat kampus UNHAS Tamalanrea.
     * Radius dalam meter.
     */
    private float $campusLat = -5.1317;
    private float $campusLng = 119.4880;
    private float $campusRadiusMeters = 1000; // 1 km radius

    /**
     * Validasi kehadiran Offline berdasarkan IP dan/atau GPS.
     * Kembalikan array ['valid' => bool, 'catatan' => string]
     */
    public function validasiLuring(string $ipAddress, ?string $locationData = null): array
    {
        $validasiIp     = $this->validateIpAddress($ipAddress);
        $validasiGps    = $locationData ? $this->validateGpsLocation($locationData) : null;

        // Jika GPS tersedia → gunakan GPS sebagai sumber kebenaran utama
        if ($validasiGps !== null) {
            return [
                'valid'   => $validasiGps,
                'catatan' => $validasiGps
                    ? "Tervalidasi via GPS: koordinat berada dalam radius kampus."
                    : "GPS menunjukkan peserta di luar area kampus.",
            ];
        }

        // Fallback ke validasi IP jika GPS tidak tersedia
        return [
            'valid'   => $validasiIp,
            'catatan' => $validasiIp
                ? "Tervalidasi via IP ({$ipAddress}): berada dalam jaringan kampus."
                : "IP ({$ipAddress}) tidak dikenali sebagai jaringan kampus.",
        ];
    }

    /**
     * Cek apakah IP berada dalam salah satu range kampus.
     */
    private function validateIpAddress(string $ip): bool
    {
        // Izinkan localhost untuk keperluan development
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return true; // Hapus/komentari baris ini di production
        }

        foreach ($this->campusIpRanges as $cidr) {
            if ($this->ipInCidr($ip, $cidr)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hitung jarak antara koordinat peserta dan pusat kampus menggunakan Haversine Formula.
     * Kembalikan true jika dalam radius yang diizinkan.
     */
    private function validateGpsLocation(string $locationData): bool
    {
        $parts = explode(',', $locationData);

        if (count($parts) !== 2) {
            return false;
        }

        $lat = (float) trim($parts[0]);
        $lng = (float) trim($parts[1]);

        // Validasi range koordinat
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return false;
        }

        $distanceMeters = $this->haversineDistance(
            $this->campusLat,
            $this->campusLng,
            $lat,
            $lng
        );

        return $distanceMeters <= $this->campusRadiusMeters;
    }

    /**
     * Haversine Formula untuk menghitung jarak dua koordinat dalam meter.
     */
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Cek apakah sebuah IP masuk dalam range CIDR.
     */
    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr);
        $ip     = ip2long($ip);
        $subnet = ip2long($subnet);

        if ($ip === false || $subnet === false) {
            return false;
        }

        $mask = -1 << (32 - (int) $bits);
        return ($ip & $mask) === ($subnet & $mask);
    }
}
```

---

### LANGKAH 4 — Update `AbsensiController`

File: `app/Http/Controllers/AbsensiController.php`

Modifikasi method `submitForm` yang sudah ada untuk menangkap data hybrid dan menjalankan validasi.

```php
<?php

namespace App\Http\Controllers;

use App\Models\KehadiranRapat;
use App\Models\Rapat;
use App\Services\LokasiValidasiService;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function __construct(
        private LokasiValidasiService $lokasiService
    ) {}

    /**
     * Method yang sudah ada — tidak diubah strukturnya.
     * Hanya ditambahkan logika hybrid di bagian penyimpanan.
     */
    public function submitForm(Request $request, string $uuid)
    {
        $rapat = Rapat::where('link_absensi', $uuid)->firstOrFail();

        // === VALIDASI REQUEST ===
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'nip_nik'           => 'nullable|string|max:50',
            'unit_kerja'        => 'required|string|max:255',
            'jabatan_tugas'     => 'required|string|max:255',
            'instansi'          => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'no_telepon'        => 'nullable|string|max:20',
            'tanda_tangan'      => 'nullable|string',
            // === FIELD BARU FITUR 1 ===
            'metode_kehadiran'  => 'nullable|in:Online,Offline',
            'location_data'     => 'nullable|string|max:100',
        ]);

        // === TANGKAP IP ADDRESS SERVER-SIDE ===
        $ipAddress = $request->ip();

        // === TENTUKAN METODE KEHADIRAN ===
        // Fallback: jika frontend tidak mengirim metode_kehadiran,
        // tentukan otomatis berdasarkan jenis_rapat di database
        $metodeKehadiran = $validated['metode_kehadiran'] ?? null;

        if (is_null($metodeKehadiran)) {
            $metodeKehadiran = match ($rapat->jenis_rapat) {
                'Luring'  => 'Offline',
                'Daring'  => 'Online',
                default   => null, // Hybrid → biarkan null sampai frontend kirim
            };
        }

        // === VALIDASI LOKASI (hanya untuk yang memilih Offline/Luring) ===
        $isLokasiValid   = null;
        $catatanValidasi = null;

        if ($metodeKehadiran === 'Offline') {
            $hasilValidasi   = $this->lokasiService->validasiLuring(
                $ipAddress,
                $validated['location_data'] ?? null
            );
            $isLokasiValid   = $hasilValidasi['valid'];
            $catatanValidasi = $hasilValidasi['catatan'];
        }

        // === SIMPAN KEHADIRAN ===
        $kehadiran = KehadiranRapat::create([
            'rapat_id'          => $rapat->id,
            'nama'              => $validated['nama'],
            'nip_nik'           => $validated['nip_nik'] ?? null,
            'unit_kerja'        => $validated['unit_kerja'],
            'jabatan_tugas'     => $validated['jabatan_tugas'],
            'instansi'          => $validated['instansi'] ?? null,
            'email'             => $validated['email'] ?? null,
            'no_telepon'        => $validated['no_telepon'] ?? null,
            'tanda_tangan'      => $validated['tanda_tangan'] ?? null,
            'status'            => 'Hadir',
            // === FIELD BARU ===
            'metode_kehadiran'  => $metodeKehadiran,
            'ip_address'        => $ipAddress,
            'location_data'     => $validated['location_data'] ?? null,
            'is_lokasi_valid'   => $isLokasiValid,
            'catatan_validasi'  => $catatanValidasi,
        ]);

        // Kembalikan response sesuai format yang sudah ada di project
        // (sesuaikan dengan return existing submitForm — redirect/JSON)
        return back()->with('success', 'Absensi berhasil dicatat.');
    }
}
```

---

### LANGKAH 5 — Buat API Endpoint untuk Frontend (HybridToggle)

Buat controller baru khusus API:

```bash
php artisan make:controller Api/HybridPresensiController
```

File: `app/Http/Controllers/Api/HybridPresensiController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rapat;
use App\Services\LokasiValidasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HybridPresensiController extends Controller
{
    public function __construct(
        private LokasiValidasiService $lokasiService
    ) {}

    /**
     * GET /api/rapat/{uuid}/jenis
     *
     * Endpoint untuk frontend mengambil jenis rapat dan
     * menentukan apakah pilihan metode kehadiran harus ditampilkan.
     *
     * Dikonsumsi oleh komponen HybridToggle milik Zahrah.
     */
    public function getJenisRapat(string $uuid): JsonResponse
    {
        $rapat = Rapat::where('link_absensi', $uuid)
            ->select(['id', 'agenda_rapat', 'jenis_rapat', 'lokasi_rapat', 'link_meeting'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => [
                'rapat_id'            => $rapat->id,
                'jenis_rapat'         => $rapat->jenis_rapat,
                'lokasi_rapat'        => $rapat->lokasi_rapat,
                'link_meeting'        => $rapat->link_meeting,
                // Flag untuk HybridToggle: apakah tampilkan pilihan Online/Offline?
                'show_hybrid_toggle'  => $rapat->jenis_rapat === 'Hybrid',
                // Default metode jika bukan Hybrid
                'default_metode'      => match ($rapat->jenis_rapat) {
                    'Luring' => 'Offline',
                    'Daring' => 'Online',
                    default  => null,
                },
            ],
        ]);
    }

    /**
     * POST /api/rapat/{uuid}/validasi-lokasi
     *
     * Endpoint untuk frontend meminta validasi lokasi secara real-time
     * SEBELUM peserta submit form (UX feedback langsung).
     *
     * Request body (JSON):
     * {
     *   "metode_kehadiran": "Offline",
     *   "location_data": "-5.1317,119.4880"  // opsional
     * }
     */
    public function validasiLokasi(Request $request, string $uuid): JsonResponse
    {
        // Pastikan rapat valid
        Rapat::where('link_absensi', $uuid)->firstOrFail();

        $validated = $request->validate([
            'metode_kehadiran' => 'required|in:Online,Offline',
            'location_data'    => 'nullable|string|max:100',
        ]);

        // Jika Online → tidak perlu validasi lokasi
        if ($validated['metode_kehadiran'] === 'Online') {
            return response()->json([
                'success'          => true,
                'is_lokasi_valid'  => true,
                'catatan'          => 'Kehadiran Online tidak memerlukan validasi lokasi.',
            ]);
        }

        // Validasi lokasi untuk Offline
        $ipAddress     = $request->ip();
        $hasilValidasi = $this->lokasiService->validasiLuring(
            $ipAddress,
            $validated['location_data'] ?? null
        );

        return response()->json([
            'success'          => true,
            'is_lokasi_valid'  => $hasilValidasi['valid'],
            'catatan'          => $hasilValidasi['catatan'],
            'ip_detected'      => $ipAddress,
        ]);
    }
}
```

---

### LANGKAH 6 — Daftarkan API Routes

File: `routes/api.php`

Jika file `api.php` belum ada, buat dulu:
```bash
php artisan install:api
```

Tambahkan routes:

```php
<?php

use App\Http\Controllers\Api\HybridPresensiController;
use Illuminate\Support\Facades\Route;

// === FITUR 1: Hybrid Presensi API ===
Route::prefix('rapat')->group(function () {
    // Ambil info jenis rapat (untuk HybridToggle frontend)
    Route::get('/{uuid}/jenis', [HybridPresensiController::class, 'getJenisRapat']);

    // Validasi lokasi real-time sebelum submit
    Route::post('/{uuid}/validasi-lokasi', [HybridPresensiController::class, 'validasiLokasi']);
});
```

> **Catatan untuk Zahrah (Frontend):**
> - `GET /api/rapat/{uuid}/jenis` → panggil saat form absensi dimuat, untuk menentukan apakah `HybridToggle` ditampilkan
> - `POST /api/rapat/{uuid}/validasi-lokasi` → panggil saat user toggle pilihan `Offline`, untuk memberikan feedback validasi lokasi real-time

---

### LANGKAH 7 — Update Filament Admin Panel

Tampilkan data metode kehadiran dan status validasi lokasi di panel admin Filament.

File: `app/Filament/Resources/KehadiranRapatResource.php`

Tambahkan kolom-kolom baru ke dalam tabel Filament:

```php
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

// Di dalam method table() → columns([...]) tambahkan:

TextColumn::make('metode_kehadiran')
    ->label('Metode')
    ->badge()
    ->color(fn (?string $state): string => match ($state) {
        'Online'  => 'info',
        'Offline' => 'success',
        default   => 'gray',
    })
    ->sortable(),

IconColumn::make('is_lokasi_valid')
    ->label('Lokasi Valid')
    ->boolean()
    ->trueIcon('heroicon-o-check-circle')
    ->falseIcon('heroicon-o-x-circle')
    ->trueColor('success')
    ->falseColor('danger')
    ->tooltip(fn ($record) => $record->catatan_validasi ?? '-'),

TextColumn::make('ip_address')
    ->label('IP Address')
    ->toggleable(isToggledHiddenByDefault: true),

TextColumn::make('catatan_validasi')
    ->label('Catatan Validasi')
    ->toggleable(isToggledHiddenByDefault: true)
    ->wrap(),
```

Tambahkan filter di Filament untuk memfilter berdasarkan metode kehadiran:

```php
use Filament\Tables\Filters\SelectFilter;

// Di dalam method table() → filters([...]) tambahkan:

SelectFilter::make('metode_kehadiran')
    ->label('Metode Kehadiran')
    ->options([
        'Online'  => 'Online (Daring)',
        'Offline' => 'Offline (Luring)',
    ]),

SelectFilter::make('is_lokasi_valid')
    ->label('Status Validasi Lokasi')
    ->options([
        '1' => 'Valid',
        '0' => 'Tidak Valid',
    ]),
```

---

### LANGKAH 8 — Konfigurasi IP Kampus (Environment-based)

Agar range IP kampus bisa dikonfigurasi tanpa menyentuh kode, tambahkan ke `.env`:

```dotenv
# === FITUR 1: Konfigurasi Lokasi Kampus UNHAS ===
CAMPUS_IP_RANGES="10.0.0.0/8,192.168.0.0/16"
CAMPUS_LAT=-5.1317
CAMPUS_LNG=119.4880
CAMPUS_RADIUS_METERS=1000
```

Update `LokasiValidasiService` untuk membaca dari env:

```php
public function __construct()
{
    $this->campusIpRanges       = explode(',', env('CAMPUS_IP_RANGES', '10.0.0.0/8'));
    $this->campusLat            = (float) env('CAMPUS_LAT', -5.1317);
    $this->campusLng            = (float) env('CAMPUS_LNG', 119.4880);
    $this->campusRadiusMeters   = (float) env('CAMPUS_RADIUS_METERS', 1000);
}
```

---

## ✅ Checklist Implementasi

Tandai setiap item setelah selesai:

- [ ] Migration `add_hybrid_fields_to_kehadiran_rapats_table` dibuat & dijalankan
- [ ] Model `KehadiranRapat` diupdate (`$fillable` + `$casts`)
- [ ] `LokasiValidasiService` dibuat di `app/Services/`
- [ ] `AbsensiController::submitForm` diupdate untuk tangkap & simpan data hybrid
- [ ] `Api/HybridPresensiController` dibuat dengan 2 endpoint
- [ ] Routes API didaftarkan di `routes/api.php`
- [ ] Filament `KehadiranRapatResource` diupdate (kolom + filter baru)
- [ ] Variabel env kampus ditambahkan ke `.env` dan `.env.example`
- [ ] `php artisan migrate` berhasil tanpa error
- [ ] Manual test: submit form absensi, cek record baru punya kolom hybrid terisi

---

## 🔌 Kontrak API untuk Frontend (Zahrah)

> Bagian ini adalah interface yang disepakati antara backend (Irgi) dan frontend (Zahrah). Jangan ubah struktur response tanpa koordinasi.

### `GET /api/rapat/{uuid}/jenis`
**Response:**
```json
{
  "success": true,
  "data": {
    "rapat_id": 1,
    "jenis_rapat": "Hybrid",
    "lokasi_rapat": "Ruang Rapat A",
    "link_meeting": "https://zoom.us/j/xxx",
    "show_hybrid_toggle": true,
    "default_metode": null
  }
}
```

### `POST /api/rapat/{uuid}/validasi-lokasi`
**Request:**
```json
{
  "metode_kehadiran": "Offline",
  "location_data": "-5.1317,119.4880"
}
```
**Response (valid):**
```json
{
  "success": true,
  "is_lokasi_valid": true,
  "catatan": "Tervalidasi via GPS: koordinat berada dalam radius kampus.",
  "ip_detected": "10.0.1.45"
}
```
**Response (tidak valid):**
```json
{
  "success": true,
  "is_lokasi_valid": false,
  "catatan": "GPS menunjukkan peserta di luar area kampus.",
  "ip_detected": "203.xxx.xxx.xxx"
}
```

### `POST /absensi/{uuid}` (form submit — existing, extended)
Tambahkan field baru pada form POST:
| Field | Tipe | Keterangan |
|-------|------|-----------|
| `metode_kehadiran` | string | `"Online"` atau `"Offline"` |
| `location_data` | string (opsional) | Format: `"lat,lng"` contoh: `"-5.1317,119.4880"` |

---

## ⚠️ Catatan Penting

1. **IP Validation di Development:** Saat localhost, IP akan `127.0.0.1`. Service sudah di-handle dengan `return true` untuk localhost. **Hapus baris ini di production.**

2. **GPS adalah opsional:** Tidak semua browser/device mengizinkan akses GPS. Sistem tetap berfungsi menggunakan fallback IP validation jika `location_data` tidak dikirim.

3. **Jangan hardcode IP UNHAS:** Selalu gunakan `.env` agar mudah diubah tanpa deploy ulang kode.

4. **Backward Compatibility:** Semua kolom baru `nullable`, sehingga data kehadiran lama tidak terpengaruh dan form absensi lama tetap berfungsi.

5. **Koordinasi Zahrah:** Komponen `HybridToggle` milik Zahrah cukup mengirim `metode_kehadiran` dan `location_data` (opsional) di body form POST. Backend sudah siap menerimanya.
