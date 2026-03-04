<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranRapat extends Model
{
    use HasFactory;

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

    // Relasi ke Rapat
    public function rapat()
    {
        return $this->belongsTo(Rapat::class, 'rapat_id');
    }
    protected $table = 'kehadiran_rapat';
}
