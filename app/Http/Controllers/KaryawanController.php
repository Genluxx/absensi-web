<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    public function halaman(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        $query = Karyawan::with('mandor')->orderBy('nama');

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
        ]);

        $targetMandor = User::findOrFail($isAdmin ? $request->mandor_id : $user->id);

        if (!in_array($targetMandor->role, ['mandor_kebun', 'mandor_pabrik'])) {
            return back()->withErrors(['mandor_id' => 'Pilih akun mandor yang valid.']);
        }

        $tipe = $targetMandor->role === 'mandor_kebun' ? 'kebun' : 'pabrik';

        Karyawan::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'tipe' => $tipe,
            'lokasi' => $targetMandor->area,
            'mandor_id' => $targetMandor->id,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan');
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