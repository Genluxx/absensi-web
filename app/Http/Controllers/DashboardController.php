<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        $karyawanQuery = Karyawan::query();
        if (!$isAdmin) {
            $karyawanQuery->where('mandor_id', $user->id);
        }
        $totalKaryawan = $karyawanQuery->count();

        $presensiHariIni = Presensi::whereDate('tanggal', now())
            ->when(!$isAdmin, fn($q) => $q->whereHas('karyawan', fn($qk) => $qk->where('mandor_id', $user->id)))
            ->get();

        $hadirHariIni = $presensiHariIni->whereIn('status', ['Hadir', 'Telat'])->count();
        $izinSakit = $presensiHariIni->whereIn('status', ['Izin', 'Sakit'])->count();
        $alpa = $presensiHariIni->where('status', 'Alpa')->count();
        $belumAbsen = max($totalKaryawan - $hadirHariIni - $izinSakit - $alpa, 0);
        $persentaseHadir = $totalKaryawan > 0 ? round(($hadirHariIni / $totalKaryawan) * 100) : 0;

        // Data grafik 7 hari terakhir
        $grafikLabel = [];
        $grafikHadir = [];
        $grafikTidakHadir = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $grafikLabel[] = $tanggal->translatedFormat('D');

            $presensiHari = Presensi::whereDate('tanggal', $tanggal->format('Y-m-d'))
                ->when(!$isAdmin, fn($q) => $q->whereHas('karyawan', fn($qk) => $qk->where('mandor_id', $user->id)))
                ->get();

            $grafikHadir[] = $presensiHari->whereIn('status', ['Hadir', 'Telat'])->count();
            $grafikTidakHadir[] = $presensiHari->whereIn('status', ['Izin', 'Sakit', 'Alpa'])->count();
        }

        return view('dashboard', compact(
            'user', 'totalKaryawan', 'hadirHariIni', 'izinSakit', 'alpa', 'belumAbsen', 'persentaseHadir',
            'grafikLabel', 'grafikHadir', 'grafikTidakHadir'
        ));
    }
}