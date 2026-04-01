<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Rapat extends Model
{
    use HasFactory;

    protected $fillable = [
        'noDokumen_rapat',
        'noRevisi_rapat',
        'tgl_berlaku_rapat',
        'agenda_rapat',
        'hari_rapat',
        'tanggal_rapat',
        'lokasi_rapat',
        'link_absensi',
        'waktu_mulai',
        'waktu_selesai',
        'jenis_rapat',    
        'link_meeting',
        'user_id', 
        'penandatangan_jabatan',
        'penandatangan_nama',
        'penandatangan_nip',        // Add this
    ];

    public function kehadirans()
    {
        return $this->hasMany(KehadiranRapat::class, 'rapat_id');
    }

    public function notulensi()
    {
        return $this->hasOne(Notulensi::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($rapat) {
            $rapat->link_absensi = Str::uuid();

            if ($rapat->tanggal_rapat) {
                $rapat->hari_rapat = Carbon::parse($rapat->tanggal_rapat)
                    ->locale('id')
                    ->translatedFormat('l');
            }
        });

        static::updating(function ($rapat) {
            if ($rapat->tanggal_rapat) {
                $rapat->hari_rapat = Carbon::parse($rapat->tanggal_rapat)
                    ->locale('id')
                    ->translatedFormat('l');
            }
        });
    }
}
