<?php

namespace App\Http\Controllers;

use App\Models\KoreksiRequest;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KoreksiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        $query = KoreksiRequest::with(['presensi.karyawan', 'mandor', 'approver'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');

        if (!$isAdmin) {
            $query->where('mandor_id', $user->id);
        }

        $daftarKoreksi = $query->paginate(15)->withQueryString();
        $presensiList = $isAdmin
            ? collect()
            : Presensi::with('karyawan')
                ->where('mandor_id', $user->id)
                ->orderByDesc('tanggal')
                ->limit(100)
                ->get();

        return view('koreksi.index', compact('user', 'isAdmin', 'daftarKoreksi', 'presensiList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'presensi_id' => 'required|exists:presensi,id',
            'status_baru' => 'nullable|in:Hadir,Izin,Sakit,Telat,Alpa',
            'keterangan_baru' => 'nullable|string',
            'alasan' => 'required|string|min:5',
        ]);

        $presensi = Presensi::findOrFail($request->presensi_id);

        // Pastikan presensi ini milik tim mandor yang login
        if ($presensi->mandor_id !== $user->id) {
            abort(403, 'Anda tidak punya akses ke data presensi ini.');
        }

        if (!$request->filled('status_baru') && !$request->filled('keterangan_baru')) {
            return back()->withErrors(['status_baru' => 'Isi status baru atau keterangan baru untuk diajukan.']);
        }

        $sudahDiajukan = KoreksiRequest::where('presensi_id', $presensi->id)
            ->where('status', 'pending')
            ->exists();

        if ($sudahDiajukan) {
            return back()->withErrors(['presensi_id' => 'Presensi ini masih memiliki permintaan koreksi yang menunggu persetujuan.']);
        }

        KoreksiRequest::create([
            'presensi_id' => $presensi->id,
            'mandor_id' => $user->id,
            'status_baru' => $request->status_baru,
            'keterangan_baru' => $request->keterangan_baru,
            'alasan' => $request->alasan,
            'status' => 'pending',
        ]);

        return redirect()->route('koreksi.index')->with('success', 'Permintaan koreksi berhasil diajukan, menunggu persetujuan.');
    }

    public function approve(KoreksiRequest $koreksi)
    {
        if ($koreksi->status !== 'pending') {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses sebelumnya.']);
        }

        $presensi = $koreksi->presensi;

        if ($koreksi->status_baru) {
            $presensi->status = $koreksi->status_baru;
        }
        if ($koreksi->keterangan_baru) {
            $presensi->keterangan = $koreksi->keterangan_baru;
        }
        $presensi->save();

        $koreksi->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'Koreksi disetujui, data presensi telah diperbarui.');
    }

    public function reject(Request $request, KoreksiRequest $koreksi)
    {
        if ($koreksi->status !== 'pending') {
            return back()->withErrors(['error' => 'Permintaan ini sudah diproses sebelumnya.']);
        }

        $request->validate([
            'catatan_admin' => 'required|string|min:3',
        ]);

        $koreksi->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', 'Permintaan koreksi ditolak.');
    }
}