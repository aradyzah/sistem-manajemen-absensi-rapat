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
                'show_hybrid_toggle'  => strtolower($rapat->jenis_rapat) === 'hybrid',
                // Default metode jika bukan Hybrid
                'default_metode'      => match (strtolower($rapat->jenis_rapat)) {
                    'luring' => 'Offline',
                    'daring' => 'Online',
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