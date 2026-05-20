<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'jam_mulai_absen',
        'jam_batas_terlambat',
        'jam_tutup_absen',
        'hari_absen',
    ];

    protected $casts = [
        'hari_absen' => 'array',
    ];

    public static function defaults(): array
    {
        return [
            'jam_mulai_absen' => '07:00:00',
            'jam_batas_terlambat' => '07:30:00',
            'jam_tutup_absen' => '09:00:00',
            'hari_absen' => [1, 2, 3, 4, 5],
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate([], self::defaults());
    }

    public function jamMulaiInput(): string
    {
        return substr($this->jam_mulai_absen, 0, 5);
    }

    public function jamBatasTerlambatInput(): string
    {
        return substr($this->jam_batas_terlambat, 0, 5);
    }

    public function jamTutupInput(): string
    {
        return substr($this->jam_tutup_absen, 0, 5);
    }

    public function hariAbsen(): array
    {
        return $this->hari_absen ?: self::defaults()['hari_absen'];
    }

    public function aktifPadaHari(int $dayOfWeek): bool
    {
        return in_array($dayOfWeek, $this->hariAbsen(), true);
    }

    public static function pilihanHari(): array
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
    }

    public function labelHariAbsen(): string
    {
        $pilihanHari = self::pilihanHari();

        return collect($this->hariAbsen())
            ->map(fn($hari) => $pilihanHari[$hari] ?? null)
            ->filter()
            ->implode(', ');
    }
}
