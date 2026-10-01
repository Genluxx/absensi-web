<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        if (!$this->foreignKeyExists('users', 'role_id', 'roles')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();
            });
        }

        if (!$this->foreignKeyExists('role_permission', 'role_id', 'roles')) {
            Schema::table('role_permission', function (Blueprint $table) {
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            });
        }

        if (!$this->foreignKeyExists('role_permission', 'permission_id', 'permissions')) {
            Schema::table('role_permission', function (Blueprint $table) {
                $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach ([
            ['users', 'role_id'],
            ['role_permission', 'role_id'],
            ['role_permission', 'permission_id'],
        ] as [$tableName, $columnName]) {
            if ($this->foreignKeyExists($tableName, $columnName)) {
                Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                    $table->dropForeign([$columnName]);
                });
            }
        }
    }

    private function foreignKeyExists(string $tableName, string $columnName, ?string $referencedTable = null): bool
    {
        $query = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $tableName)
            ->where('COLUMN_NAME', $columnName)
            ->whereNotNull('REFERENCED_TABLE_NAME');

        if ($referencedTable !== null) {
            $query->where('REFERENCED_TABLE_NAME', $referencedTable);
        }

        return $query->exists();
    }
};
