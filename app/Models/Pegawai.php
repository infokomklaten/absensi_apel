<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;

class Pegawai extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pegawai';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'unit_kerja',
        'role',
        'password',
        'aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'aktif' => 'boolean',
    ];

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function absensiHariIni()
    {
        return $this->absensi()->whereDate('tanggal', today())->first();
    }
}
