<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        // Akun login
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'SYAHRUL RAMADHAN',
                'email' => 'admin@absensiweb.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin_hr',
                'area' => null,
            ]
        );

        $mandorKebun1 = User::firstOrCreate(
            ['username' => 'mandorkebun1'],
            [
                'name' => 'Ahmad Dahlan',
                'email' => 'mandorkebun1@absensiweb.test',
                'password' => Hash::make('password123'),
                'role' => 'mandor_kebun',
                'area' => 'Divisi I - Blok A',
            ]
        );

        $mandorKebun2 = User::firstOrCreate(
            ['username' => 'mandorkebun2'],
            [
                'name' => 'Slamet Riyadi',
                'email' => 'mandorkebun2@absensiweb.test',
                'password' => Hash::make('kebun123'),
                'role' => 'mandor_kebun',
                'area' => 'Divisi II - Blok B',
            ]
        );

        $mandorPabrik1 = User::firstOrCreate(
            ['username' => 'mandorpabrik1'],
            [
                'name' => 'Joko Susilo',
                'email' => 'mandorpabrik1@absensiweb.test',
                'password' => Hash::make('password123'),
                'role' => 'mandor_pabrik',
                'area' => 'Stasiun Rebusan & Press',
            ]
        );

        $mandorPabrik2 = User::firstOrCreate(
            ['username' => 'mandorpabrik2'],
            [
                'name' => 'Bambang Wijaya',
                'email' => 'mandorpabrik2@absensiweb.test',
                'password' => Hash::make('pabrik123'),
                'role' => 'mandor_pabrik',
                'area' => 'Stasiun Kernel & Boiler',
            ]
        );

        // Karyawan
        $k1 = Karyawan::firstOrCreate(['nik' => 'NIK-1001'], ['nama' => 'Ujang Supriatna', 'jabatan' => 'Pemanen Utama', 'tipe' => 'kebun', 'lokasi' => 'Divisi I - Blok A', 'mandor_id' => $mandorKebun1->id]);
        $k2 = Karyawan::firstOrCreate(['nik' => 'NIK-1002'], ['nama' => 'Sutarman', 'jabatan' => 'Pemanen', 'tipe' => 'kebun', 'lokasi' => 'Divisi I - Blok A', 'mandor_id' => $mandorKebun1->id]);
        $k3 = Karyawan::firstOrCreate(['nik' => 'NIK-1003'], ['nama' => 'Rudi Tabuti', 'jabatan' => 'Pemuat FFB', 'tipe' => 'kebun', 'lokasi' => 'Divisi I - Blok A', 'mandor_id' => $mandorKebun1->id]);
        Karyawan::firstOrCreate(['nik' => 'NIK-1004'], ['nama' => 'Dede Inoen', 'jabatan' => 'Pemanen Utama', 'tipe' => 'kebun', 'lokasi' => 'Divisi II - Blok B', 'mandor_id' => $mandorKebun2->id]);
        Karyawan::firstOrCreate(['nik' => 'NIK-1005'], ['nama' => 'Mamat Alkatiri', 'jabatan' => 'Perawat Tanaman', 'tipe' => 'kebun', 'lokasi' => 'Divisi II - Blok B', 'mandor_id' => $mandorKebun2->id]);
        $k6 = Karyawan::firstOrCreate(['nik' => 'NIK-2001'], ['nama' => 'Eko Prasetyo', 'jabatan' => 'Operator Sterilizer', 'tipe' => 'pabrik', 'lokasi' => 'Stasiun Rebusan & Press', 'mandor_id' => $mandorPabrik1->id]);
        $k7 = Karyawan::firstOrCreate(['nik' => 'NIK-2002'], ['nama' => 'Agus Kuncoro', 'jabatan' => 'Operator Press Engine', 'tipe' => 'pabrik', 'lokasi' => 'Stasiun Rebusan & Press', 'mandor_id' => $mandorPabrik1->id]);
        Karyawan::firstOrCreate(['nik' => 'NIK-2003'], ['nama' => 'Herman Felani', 'jabatan' => 'Operator Boiler', 'tipe' => 'pabrik', 'lokasi' => 'Stasiun Kernel & Boiler', 'mandor_id' => $mandorPabrik2->id]);
        Karyawan::firstOrCreate(['nik' => 'NIK-2004'], ['nama' => 'Deni Sumargo', 'jabatan' => 'Teknisi Maintenance', 'tipe' => 'pabrik', 'lokasi' => 'Stasiun Kernel & Boiler', 'mandor_id' => $mandorPabrik2->id]);
        Karyawan::firstOrCreate(['nik' => 'NIK-2005'], ['nama' => 'Rian Hidayat', 'jabatan' => 'Helper Laboratorium', 'tipe' => 'pabrik', 'lokasi' => 'Stasiun Rebusan & Press', 'mandor_id' => $mandorPabrik1->id]);

        // Riwayat presensi 7 hari terakhir
        $semuaKaryawan = [
            ['karyawan' => $k1, 'mandor' => $mandorKebun1->id],
            ['karyawan' => $k2, 'mandor' => $mandorKebun1->id],
            ['karyawan' => $k3, 'mandor' => $mandorKebun1->id],
            ['karyawan' => $k6, 'mandor' => $mandorPabrik1->id],
            ['karyawan' => $k7, 'mandor' => $mandorPabrik1->id],
        ];

        $polaHarian = [
            ['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Izin'],
            ['Hadir', 'Hadir', 'Hadir', 'Telat', 'Hadir'],
            ['Hadir', 'Hadir', 'Sakit', 'Hadir', 'Hadir'],
            ['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Hadir'],
            ['Hadir', 'Telat', 'Hadir', 'Hadir', 'Alpa'],
            ['Hadir', 'Hadir', 'Hadir', 'Izin', 'Hadir'],
            ['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Hadir'],
        ];

        for ($hariKe = 0; $hariKe < 7; $hariKe++) {
            $jarakHari = 6 - $hariKe;
            $tanggal = now()->subDays($jarakHari)->format('Y-m-d');

            foreach ($semuaKaryawan as $i => $data) {
                $status = $polaHarian[$hariKe][$i];
                $jamMasuk = in_array($status, ['Hadir', 'Telat']) ? '06:30:00' : null;

                Presensi::firstOrCreate(
                    [
                        'karyawan_id' => $data['karyawan']->id,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'jam_masuk' => $jamMasuk,
                        'status' => $status,
                        'keterangan' => 'Presensi harian',
                        'mandor_id' => $data['mandor'],
                    ]
                );
            }
        }
    }
}