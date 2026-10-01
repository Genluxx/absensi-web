<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    public function halaman(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        $query = Karyawan::with(['mandor', 'user', 'presensi' => function ($query) {
            $query->whereDate('tanggal', now());
        }])->orderBy('nama');

        if (!$isAdmin) {
            $query->where('mandor_id', $user->id);
        }

        if ($request->cari) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->cari.'%')
                  ->orWhere('nik', 'like', '%'.$request->cari.'%');
            });
        }

        $karyawan = $query->paginate(15)->withQueryString();

        $mandorList = $isAdmin
            ? User::whereIn('role', ['mandor_kebun', 'mandor_pabrik'])->orderBy('name')->get()
            : collect();

        return view('karyawan.index', compact('user', 'karyawan', 'isAdmin', 'mandorList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        $request->validate([
            'nik' => 'required|string|unique:karyawan,nik',
            'nama' => 'required|string',
            'jabatan' => 'nullable|string',
            'mandor_id' => ($isAdmin ? 'required' : 'nullable').'|exists:users,id',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $targetMandor = User::findOrFail($isAdmin ? $request->mandor_id : $user->id);

        if (!in_array($targetMandor->role, ['mandor_kebun', 'mandor_pabrik'])) {
            return back()->withErrors(['mandor_id' => 'Pilih akun mandor yang valid.']);
        }

        $tipe = $targetMandor->role === 'mandor_kebun' ? 'kebun' : 'pabrik';

        $karyawan = DB::transaction(function () use ($request, $targetMandor, $tipe) {
            $role = \App\Models\Role::where('slug', 'karyawan')->firstOrFail();
            $account = User::create([
                'name' => $request->nama,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'karyawan',
                'role_id' => $role->id,
                'area' => $targetMandor->area,
            ]);

            return Karyawan::create([
                'nik' => $request->nik,
                'nama' => $request->nama,
                'jabatan' => $request->jabatan,
                'tipe' => $tipe,
                'lokasi' => $targetMandor->area,
                'mandor_id' => $targetMandor->id,
                'user_id' => $account->id,
            ]);
        });

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function quickAttendance(Request $request, Karyawan $karyawan)
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        if (!$isAdmin && $karyawan->mandor_id !== $user->id) {
            abort(403, 'Anda tidak punya akses ke data karyawan ini.');
        }

        $request->validate([
            'status' => 'required|in:Hadir,Izin,Cuti,Sakit',
        ]);

        $waktuAbsen = now();
        $status = $request->status;
        $jamMasuk = $status === 'Hadir' ? $waktuAbsen->format('H:i:s') : null;
        $keterangan = null;

        if ($status === 'Hadir' && $waktuAbsen->format('H:i:s') > '08:00:00') {
            $status = 'Telat';
            $keterangan = 'Terlambat absen setelah batas wajib 08:00';
        }

        Presensi::updateOrCreate(
            ['karyawan_id' => $karyawan->id, 'tanggal' => $waktuAbsen->format('Y-m-d')],
            [
                'jam_masuk' => $jamMasuk,
                'status' => $status,
                'keterangan' => $keterangan,
                'mandor_id' => $karyawan->mandor_id,
            ]
        );

        return redirect()->route('karyawan.index')->with('success', "Status {$karyawan->nama} berhasil dicatat sebagai {$status}.");
    }

    public function createAccount(Request $request, Karyawan $karyawan)
    {
        $user = Auth::user();
        if (!$user->hasPermission('data.view_all')) {
            abort(403, 'Anda tidak punya akses ke data karyawan ini.');
        }

        if ($karyawan->user_id) {
            return back()->withErrors(['akun' => 'Karyawan ini sudah memiliki akun.']);
        }

        $request->validate([
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $role = \App\Models\Role::where('slug', 'karyawan')->firstOrFail();
        $account = User::create([
            'name' => $karyawan->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'karyawan',
            'role_id' => $role->id,
            'area' => $karyawan->lokasi,
        ]);

        $karyawan->update(['user_id' => $account->id]);

        return redirect()->route('karyawan.index')->with('success', 'Akun karyawan berhasil dibuat.');
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $user = Auth::user();
        if (!$user->hasPermission('data.view_all') && $karyawan->mandor_id !== $user->id) {
            abort(403, 'Anda tidak punya akses ke data karyawan ini.');
        }

        $request->validate([
            'nik' => 'required|string|unique:karyawan,nik,'.$karyawan->id,
            'nama' => 'required|string',
            'jabatan' => 'nullable|string',
        ]);

        $karyawan->update($request->only(['nik', 'nama', 'jabatan']));

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui');
    }

    public function destroy(Karyawan $karyawan)
    {
        $user = Auth::user();
        if (!$user->hasPermission('data.view_all') && $karyawan->mandor_id !== $user->id) {
            abort(403, 'Anda tidak punya akses ke data karyawan ini.');
        }

        if ($karyawan->presensi()->exists()) {
            return redirect()->route('karyawan.index')
                ->withErrors(['error' => 'Karyawan ini masih punya riwayat presensi, tidak bisa dihapus.']);
        }

        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus');
    } 
}