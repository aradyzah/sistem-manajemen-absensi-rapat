<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notulensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'rapat_id',
        'pimpinan_rapat',
        'sekretaris',
        'isi_notulensi',
        'daftar_keputusan',
        'tindak_lanjut',
    ];

    /**
     * Relasi balik ke Rapat
     */
    public function rapat()
    {
        return $this->belongsTo(Rapat::class);
    }
}
