<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE presensi MODIFY status ENUM('Hadir', 'Izin', 'Sakit', 'Cuti', 'Telat', 'Alpa') NOT NULL");
    }

    public function down(): void
    {
        DB::table('presensi')->where('status', 'Cuti')->update(['status' => 'Izin']);
        DB::statement("ALTER TABLE presensi MODIFY status ENUM('Hadir', 'Izin', 'Sakit', 'Telat', 'Alpa') NOT NULL");
    }
};
