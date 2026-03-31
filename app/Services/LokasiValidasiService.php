<?php

namespace App\Services;

class LokasiValidasiService
{
    /**
     * Daftar range IP kampus UNHAS.
     * Sesuaikan dengan range IP resmi kampus.
     * Format CIDR: ['10.0.0.0/8', '192.168.1.0/24', ...]
     */
    private array $campusIpRanges;
    private float $campusLat;
    private float $campusLng;
    private float $campusRadiusMeters;

    public function __construct()
    {
        $this->campusIpRanges       = explode(',', env('CAMPUS_IP_RANGES', '10.0.0.0/8'));
        $this->campusLat            = (float) env('CAMPUS_LAT', -5.1317);
        $this->campusLng            = (float) env('CAMPUS_LNG', 119.4880);
        $this->campusRadiusMeters   = (float) env('CAMPUS_RADIUS_METERS', 1000);
    }

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