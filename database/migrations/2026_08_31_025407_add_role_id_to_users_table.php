<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'super_admin' ke enum role lama (biar akun lama tetap kompatibel)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin_hr', 'mandor_kebun', 'mandor_pabrik') DEFAULT 'mandor_kebun'");

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained('roles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_hr', 'mandor_kebun', 'mandor_pabrik') DEFAULT 'mandor_kebun'");
    }
};