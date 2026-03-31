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

    // Menampilkan form absensi berdasarkan UUID
    public function showForm($uuid)
    {
        $rapat = Rapat::where('link_absensi', $uuid)->firstOrFail();
        return view('absensi.form', compact('rapat'));
    }

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

    // Untuk autofill by NIP/NIK
    public function getPesertaByNip(Request $request, $uuid)
    {
        $rapat = Rapat::where('link_absensi', $uuid)->firstOrFail();

        $peserta = KehadiranRapat::where('rapat_id', $rapat->id)
            ->where('nip_nik', $request->nip_nik)
            ->first();

        return response()->json($peserta);
    }
}
