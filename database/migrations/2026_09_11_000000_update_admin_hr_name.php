<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'admin_hr')
            ->update(['name' => 'SYAHRUL RAMADHAN']);
    }

    public function down(): void
    {
        // The previous account names are not reliably distinguishable after migration.
    }
};