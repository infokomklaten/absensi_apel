<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_settings', function (Blueprint $table) {
            $table->json('hari_absen')->nullable()->after('jam_tutup_absen');
        });

        DB::table('absensi_settings')->update([
            'hari_absen' => json_encode([1, 2, 3, 4, 5]),
        ]);
    }

    public function down(): void
    {
        Schema::table('absensi_settings', function (Blueprint $table) {
            $table->dropColumn('hari_absen');
        });
    }
};
