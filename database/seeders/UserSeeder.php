<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'adminhr'],
            [
                'name' => 'SYAHRUL RAMADHAN',
                'email' => 'adminhr@sawita.test',
                'password' => Hash::make('password123'),
                'role' => 'admin_hr',
                'area' => null,
            ]
        );

        User::firstOrCreate(
            ['username' => 'mandorkebun1'],
            [
                'name' => 'Slamet Riyadi',
                'email' => 'mandorkebun1@sawita.test',
                'password' => Hash::make('password123'),
                'role' => 'mandor_kebun',
                'area' => 'Divisi I',
            ]
        );

        User::firstOrCreate(
            ['username' => 'mandorpabrik1'],
            [
                'name' => 'Budi Santoso',
                'email' => 'mandorpabrik1@sawita.test',
                'password' => Hash::make('pabrik123'),
                'role' => 'mandor_pabrik',
                'area' => 'Stasiun Rebusan (Sterilizer)',
            ]
        );
    }
}