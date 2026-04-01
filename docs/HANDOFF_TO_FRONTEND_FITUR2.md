# 📝 Handoff untuk Frontend — Fitur 2: Modul Notulensi Rapat

> **Untuk:** Zahrah (Frontend Developer)
> **Dari:** Irgi (Backend Developer)
> **Status Backend:** **SELESAI** ✅

Dokumen ini menjelaskan struktur data untuk fitur Notulensi yang baru saja diimplementasikan.

---

## 🎯 Konteks
Setiap rapat kini memiliki tepat satu **Notulensi**. Data ini diisi oleh admin melalui dashboard Filament menggunakan Rich Text Editor.

---

## 🗄️ Struktur Data (Model Notulensi)
Jika kamu memanggil data Rapat beserta relasi notulensinya, berikut adalah field yang tersedia:

| Field | Tipe | Keterangan |
|-------|------|-----------|
| `pimpinan_rapat` | string | Nama pimpinan yang memimpin rapat |
| `sekretaris` | string | Nama notulis/sekretaris rapat |
| `isi_notulensi` | longtext (HTML) | Detail pembahasan rapat (format HTML dari RichEditor) |
| `daftar_keputusan` | longtext (HTML) | Poin-poin keputusan rapat (format HTML) |
| `tindak_lanjut` | longtext (HTML) | Poin-poin rencana tindak lanjut (format HTML) |

---

## 🎨 Panduan Implementasi Frontend

### 1. Menampilkan Konten HTML
Karena data `isi_notulensi`, `daftar_keputusan`, dan `tindak_lanjut` dikirim dalam format HTML (dari RichEditor), pastikan kamu menampilkannya menggunakan directive `{!! $data !!}` di Blade atau `v-html` jika menggunakan Vue/JS.

**Contoh di Blade:**
```html
<div class="prose max-w-none">
    {!! $rapat->notulensi->isi_notulensi !!}
</div>
```

### 2. Styling (Tailwind Typography)
Sangat disarankan menggunakan plugin **Tailwind CSS Typography** (`prose`) agar tampilan teks dari RichEditor (seperti list, bold, italic) terlihat rapi secara otomatis.

```html
<article class="prose lg:prose-xl">
  {{-- Konten Notulensi di sini --}}
</article>
```

---

## 🔌 Rencana API (Optional)
Jika kamu butuh mengambil data notulensi via AJAX, saya bisa sediakan endpoint:
`GET /api/rapat/{uuid}/notulensi`

**Contoh JSON Response:**
```json
{
  "success": true,
  "data": {
    "agenda": "Rapat Koordinasi",
    "notulensi": {
        "pimpinan_rapat": "Dr. Ir. Budi",
        "sekretaris": "Siti Aminah",
        "isi_notulensi": "<p>Pembahasan mengenai...</p>",
        "daftar_keputusan": "<ul><li>Poin 1...</li></ul>"
    }
  }
}
```

Beritahu saya jika kamu butuh API tersebut diaktifkan!
