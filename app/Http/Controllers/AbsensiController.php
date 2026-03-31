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

        // === SIMPAN KEHADIRAN (Update if exists, Create if new) ===
        try {
            $searchData = [
                'rapat_id' => $rapat->id,
            ];

            // Jika NIP diisi, gunakan NIP sebagai kunci pencarian untuk update
            // Jika NIP kosong (eksternal), biarkan sistem membuat record baru
            if (!empty($validated['nip_nik'])) {
                $searchData['nip_nik'] = $validated['nip_nik'];
            } else {
                // Untuk eksternal tanpa NIP, tambahkan kunci unik lain jika perlu, 
                // sementara kita biarkan create baru dengan menambahkan dummy unik atau cukup create.
                // Namun karena ada constraint unique di DB (NIP-Rapat), NIP null biasanya tidak dianggap duplikat oleh DB.
                $searchData['nip_nik'] = null;
                $searchData['nama'] = $validated['nama']; // Tambahan agar eksternal tidak tertukar
            }

            $kehadiran = KehadiranRapat::updateOrCreate(
                $searchData,
                [
                    'nama'              => $validated['nama'],
                    'unit_kerja'        => $validated['unit_kerja'],
                    'jabatan_tugas'     => $validated['jabatan_tugas'],
                    'instansi'          => $validated['instansi'] ?? null,
                    'email'             => $validated['email'] ?? null,
                    'no_telepon'        => $validated['no_telepon'] ?? null,
                    'tanda_tangan'      => $validated['tanda_tangan'] ?? null,
                    'status'            => 'Hadir',
                    'metode_kehadiran'  => $metodeKehadiran,
                    'ip_address'        => $ipAddress,
                    'location_data'     => $validated['location_data'] ?? null,
                    'is_lokasi_valid'   => $isLokasiValid,
                    'catatan_validasi'  => $catatanValidasi,
                ]
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Absensi berhasil dicatat. Terima kasih!'
                ]);
            }

            return back()->with('success', 'Absensi berhasil dicatat. Terima kasih!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menyimpan absensi.');
        }
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
