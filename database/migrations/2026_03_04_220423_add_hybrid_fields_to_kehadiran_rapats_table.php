<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kehadiran_rapat', function (Blueprint $table) {
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kehadiran_rapat', function (Blueprint $table) {
            $table->dropColumn([
                'metode_kehadiran',
                'ip_address',
                'location_data',
                'is_lokasi_valid',
                'catatan_validasi',
            ]);
        });
    }
};
