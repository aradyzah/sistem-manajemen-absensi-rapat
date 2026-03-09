# 📝 Handoff untuk Frontend — Fitur 1: Modul Presensi Hybrid

> **Untuk:** Zahrah (Frontend Developer)
> **Dari:** Irgi (Backend Developer)
> **Status Backend:** **SELESAI** ✅

Dokumen ini menjelaskan cara mengintegrasikan komponen `HybridToggle` di sisi frontend dengan backend yang sudah siap.

---

## 🎯 Konteks

Backend telah di-update untuk mendukung tiga jenis rapat: Luring, Daring, dan Hybrid. Untuk rapat **Hybrid**, frontend perlu menampilkan pilihan kepada user agar mereka bisa memilih metode kehadiran (Online atau Offline).

Backend sudah menyediakan semua yang kamu butuhkan melalui API.

---

## 🔌 Kontrak API & Panduan Implementasi

Berikut adalah panduan step-by-step untuk implementasi di sisi frontend.

### Langkah 1: Tentukan Apakah Pilihan Hybrid Perlu Muncul

Saat halaman form absensi (`/absensi/{uuid}`) dimuat, buatlah request `GET` ke endpoint berikut untuk mendapatkan informasi jenis rapat.

- **Endpoint:** `GET /api/rapat/{uuid}/jenis`
- **Tujuan:** Mengetahui apakah rapat ini 'Hybrid' dan apakah toggle Online/Offline perlu ditampilkan.

**Contoh Response (untuk rapat Hybrid):**
```json
{
  "success": true,
  "data": {
    "rapat_id": 123,
    "jenis_rapat": "Hybrid",
    "lokasi_rapat": "Ruang Rapat A",
    "link_meeting": "https://zoom.us/j/xyz",
    "show_hybrid_toggle": true, // 👈 Gunakan flag ini
    "default_metode": null
  }
}
```
**Contoh Response (untuk rapat Luring):**
```json
{
  "success": true,
  "data": {
    "rapat_id": 124,
    "jenis_rapat": "Luring",
    "lokasi_rapat": "Ruang Rapat B",
    "link_meeting": null,
    "show_hybrid_toggle": false, // 👈 Toggle tidak perlu tampil
    "default_metode": "Offline"
  }
}
```

**Logika di Frontend:**
- Jika `data.show_hybrid_toggle` adalah `true`, maka tampilkan komponen `HybridToggle` (pilihan Online/Offline) kepada user.
- Jika `false`, tidak perlu menampilkan apa-apa.

---

### Langkah 2: Kirim Pilihan User Saat Submit Form

Saat user menekan tombol "Submit" pada form absensi utama, kamu perlu menambahkan satu field baru ke dalam data `POST` yang dikirim ke endpoint `POST /absensi/{uuid}`.

- **Field Baru:** `metode_kehadiran`
- **Nilai:** `'Online'` atau `'Offline'` (sesuai pilihan user).

**Contoh Form Data yang Dikirim:**
```
nama=John+Doe
nip_nik=12345
...
tanda_tangan=...
metode_kehadiran=Offline  // 👈 Field baru ditambahkan di sini
```

Backend akan secara otomatis menangani sisanya, termasuk validasi lokasi jika user memilih 'Offline'.

---

### Langkah 3 (Opsional, untuk UX Lebih Baik): Validasi Lokasi Real-time

Untuk memberikan feedback langsung kepada user *sebelum* mereka submit form, kamu bisa memanggil endpoint validasi saat user memilih "Offline".

- **Endpoint:** `POST /api/rapat/{uuid}/validasi-lokasi`
- **Tujuan:** Mengecek apakah lokasi user (berdasarkan IP atau GPS) valid untuk kehadiran Offline.
- **Kapan dipanggil:** Saat user memilih/mengklik opsi "Offline" pada `HybridToggle`.

**Request Body (JSON):**
```json
{
  "metode_kehadiran": "Offline",
  "location_data": "-5.1317,119.4880" // Opsional, kirim jika browser bisa mendapatkan koordinat GPS
}
```

**Contoh Response (Lokasi Valid):**
```json
{
  "success": true,
  "is_lokasi_valid": true,
  "catatan": "Tervalidasi via GPS: koordinat berada dalam radius kampus.",
  "ip_detected": "10.0.1.45"
}
```

**Contoh Response (Lokasi Tidak Valid):**
```json
{
  "success": true,
  "is_lokasi_valid": false,
  "catatan": "IP (203.x.x.x) tidak dikenali sebagai jaringan kampus.",
  "ip_detected": "203.x.x.x"
}
```

Kamu bisa menampilkan pesan dari field `catatan` kepada user secara real-time.

---

## ✅ Kesimpulan untuk Frontend

1.  **Panggil `GET /api/rapat/{uuid}/jenis`** saat form load.
2.  Jika `show_hybrid_toggle: true`, **tampilkan pilihan** Online/Offline.
3.  Saat form utama di-submit, **sertakan field `metode_kehadiran`** dalam request.

Dengan mengikuti panduan ini, pekerjaan frontend bisa dilanjutkan dengan lancar.
