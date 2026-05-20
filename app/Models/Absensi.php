<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'waktu_absen',
        'foto',
        'latitude',
        'longitude',
        'alamat_lokasi',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'hadir' => 'Hadir',
            'terlambat' => 'Hadir',
            'alpha' => 'Belum Presensi',
            default => 'Tidak Diketahui',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'hadir' => 'hadir',
            'terlambat' => 'hadir',
            'alpha' => 'alpha',
            default => 'secondary',
        };
    }
}
