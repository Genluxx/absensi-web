<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'karyawan') {
            $karyawan = $user->dataKaryawan;
            abort_unless($karyawan, 403, 'Data karyawan belum terhubung ke akun ini.');
            $presensiHariIni = Presensi::whereDate('tanggal', now())
                ->where('karyawan_id', $karyawan->id)
                ->first();
            $cutiTerpakai = Presensi::where('karyawan_id', $karyawan->id)
                ->where('status', 'Cuti')
                ->whereYear('tanggal', now()->year)
                ->count();
            $cutiTersisa = max(12 - $cutiTerpakai, 0);
            $statusHariIni = $presensiHariIni?->status;
            if (!$statusHariIni && now()->format('H:i:s') > '08:00:00') {
                $statusHariIni = 'Alpa';
            }

            return view('dashboard-karyawan', compact('user', 'karyawan', 'presensiHariIni', 'cutiTerpakai', 'cutiTersisa', 'statusHariIni'));
        }

        $isAdmin = $user->hasPermission('data.view_all');

        $mandorList = collect();
        $assignableRoles = collect();
        if (in_array($user->role, ['admin_hr', 'super_admin'], true)) {
            $mandorList = User::whereNotIn('role', ['super_admin', 'admin_hr', 'karyawan'])
                ->orderBy('name')->get();
            $assignableRoles = Role::whereNotIn('slug', ['super_admin', 'admin_hr', 'karyawan'])
                ->orderBy('name')->get();
        }

        $karyawanQuery = Karyawan::query();
        if (!$isAdmin) {
            $karyawanQuery->where('mandor_id', $user->id);
        }
        $totalKaryawan = $karyawanQuery->count();

        $presensiHariIni = Presensi::whereDate('tanggal', now())
            ->when(!$isAdmin, fn($q) => $q->whereHas('karyawan', fn($qk) => $qk->where('mandor_id', $user->id)))
            ->get();

        $hadirHariIni = $presensiHariIni->whereIn('status', ['Hadir', 'Telat'])->count();
        $izinSakit = $presensiHariIni->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count();
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
            $grafikTidakHadir[] = $presensiHari->whereIn('status', ['Izin', 'Sakit', 'Cuti', 'Alpa'])->count();
        }

        return view('dashboard', compact(
            'user', 'totalKaryawan', 'hadirHariIni', 'izinSakit', 'alpa', 'belumAbsen', 'persentaseHadir',
            'grafikLabel', 'grafikHadir', 'grafikTidakHadir'
            , 'mandorList', 'assignableRoles'
        ));
    }
}