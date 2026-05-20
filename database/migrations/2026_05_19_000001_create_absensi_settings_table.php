<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_settings', function (Blueprint $table) {
            $table->id();
            $table->time('jam_mulai_absen')->default('07:00:00');
            $table->time('jam_batas_terlambat')->default('07:30:00');
            $table->time('jam_tutup_absen')->default('09:00:00');
            $table->timestamps();
        });

        DB::table('absensi_settings')->insert([
            'jam_mulai_absen' => '07:00:00',
            'jam_batas_terlambat' => '07:30:00',
            'jam_tutup_absen' => '09:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_settings');
    }
};
