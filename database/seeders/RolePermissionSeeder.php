<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat daftar permission dasar
        $permissions = [
            ['name' => 'Lihat Karyawan', 'slug' => 'karyawan.view', 'group' => 'Karyawan'],
            ['name' => 'Tambah Karyawan', 'slug' => 'karyawan.create', 'group' => 'Karyawan'],
            ['name' => 'Edit Karyawan', 'slug' => 'karyawan.edit', 'group' => 'Karyawan'],
            ['name' => 'Hapus Karyawan', 'slug' => 'karyawan.delete', 'group' => 'Karyawan'],
            ['name' => 'Input Presensi', 'slug' => 'presensi.input', 'group' => 'Presensi'],
            ['name' => 'Lihat Log Presensi', 'slug' => 'presensi.view', 'group' => 'Presensi'],
            ['name' => 'Export Laporan', 'slug' => 'presensi.export', 'group' => 'Presensi'],
            ['name' => 'Ajukan Koreksi Presensi', 'slug' => 'koreksi.request', 'group' => 'Koreksi'],
            ['name' => 'Setujui/Tolak Koreksi', 'slug' => 'koreksi.approve', 'group' => 'Koreksi'],
            ['name' => 'Kelola Akun Mandor', 'slug' => 'mandor.manage', 'group' => 'Administrasi'],
            ['name' => 'Kelola Role & Permission', 'slug' => 'role.manage', 'group' => 'Administrasi'],
            ['name' => 'Lihat Data Semua Tim', 'slug' => 'data.view_all', 'group' => 'Administrasi'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], $p);
        }

        // 2. Buat role sistem (bawaan, nggak bisa dihapus)
        $adminHr = Role::firstOrCreate(
            ['slug' => 'admin_hr'],
            ['name' => 'Admin HR', 'is_system' => true]
        );
        $mandorKebun = Role::firstOrCreate(
            ['slug' => 'mandor_kebun'],
            ['name' => 'Mandor Kebun', 'is_system' => true]
        );
        $mandorPabrik = Role::firstOrCreate(
            ['slug' => 'mandor_pabrik'],
            ['name' => 'Mandor Pabrik', 'is_system' => true]
        );

        // 3. Assign permission ke tiap role sesuai spek terbaru
        $adminHr->permissions()->sync(
            Permission::whereIn('slug', [
                'karyawan.view', 'karyawan.create', 'karyawan.edit', 'karyawan.delete',
                'presensi.view', 'presensi.export',
                'koreksi.approve',
                'mandor.manage',
            ])->pluck('id')
        );

        $permMandor = Permission::whereIn('slug', [
            'presensi.input', 'presensi.view', 'koreksi.request', 'karyawan.view',
        ])->pluck('id');

        $mandorKebun->permissions()->sync($permMandor);
        $mandorPabrik->permissions()->sync(Permission::pluck('id'));

        // 4. Hubungkan users lama ke role_id sesuai kolom role string mereka
        User::where('role', 'admin_hr')->update(['role_id' => $adminHr->id]);
        User::where('role', 'mandor_kebun')->update(['role_id' => $mandorKebun->id]);
        User::where('role', 'mandor_pabrik')->update(['role_id' => $mandorPabrik->id]);

        $adminHr->permissions()->sync(
            Permission::whereIn('slug', [
                'karyawan.view', 'karyawan.create', 'karyawan.edit', 'karyawan.delete',
                'presensi.view', 'presensi.export',
                'koreksi.approve',
                'mandor.manage',
                'data.view_all',
            ])->pluck('id')
        );
        
        // 5. Buat akun Super Admin (kalau belum ada)
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@sawita.test',
                'password' => Hash::make('superadmin123'),
                'role' => 'super_admin',
                'area' => null,
            ]
        );
    }
}