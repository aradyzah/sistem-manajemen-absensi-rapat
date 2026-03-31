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