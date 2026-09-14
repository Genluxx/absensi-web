<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function inputForm(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        if ($isAdmin) {
            $mandorList = User::whereIn('role', ['mandor_kebun', 'mandor_pabrik'])
                ->orderBy('name')->get();

            $mandorId = $request->mandor_id;

            $karyawan = $mandorId
                ? Karyawan::where('mandor_id', $mandorId)->orderBy('nama')->get()
                : collect();

            return view('presensi.input', compact('user', 'karyawan', 'isAdmin', 'mandorList', 'mandorId'));
        }

        $karyawan = Karyawan::where('mandor_id', $user->id)->orderBy('nama')->get();

        return view('presensi.input', [
            'user' => $user,
            'karyawan' => $karyawan,
            'isAdmin' => false,
            'mandorList' => collect(),
            'mandorId' => null,
        ]);
    }

    public function simpan(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        $mandorId = $isAdmin ? $request->input('mandor_id') : $user->id;

        if ($isAdmin && !$mandorId) {
            return back()->withErrors(['mandor_id' => 'Pilih tim mandor terlebih dahulu.']);
        }

        $data = $request->input('presensi', []);

        // Whitelist: hanya karyawan_id milik tim yang dipilih yang boleh diproses
        $karyawanIdsMilikTim = Karyawan::where('mandor_id', $mandorId)->pluck('id')->toArray();

        foreach ($data as $karyawanId => $item) {
            if (empty($item['status'])) {
                continue;
            }

            if (!in_array((int) $karyawanId, $karyawanIdsMilikTim)) {
                continue; // cegah input presensi ke karyawan tim lain
            }

            $jamMasuk = in_array($item['status'], ['Hadir', 'Telat']) ? now()->format('H:i:s') : null;

            $updateData = [
                'jam_masuk' => $jamMasuk,
                'status' => $item['status'],
                'keterangan' => $item['keterangan'] ?? null,
                'mandor_id' => $mandorId,
            ];

            // GPS - dikirim dari JS (navigator.geolocation + reverse geocode)
            if (!empty($item['latitude']) && !empty($item['longitude'])) {
                $updateData['latitude'] = $item['latitude'];
                $updateData['longitude'] = $item['longitude'];
                $updateData['lokasi_gps'] = $item['lokasi_gps'] ?? null;
            }

            // Foto dari kamera (input file)
            if ($request->hasFile("presensi.$karyawanId.foto")) {
                $path = $request->file("presensi.$karyawanId.foto")->store('presensi-foto', 'public');
                $updateData['foto_path'] = $path;

                if (!empty($updateData['latitude']) && !empty($updateData['longitude'])) {
                    $this->tempelWatermarkGps(
                        storage_path('app/public/'.$path),
                        $updateData['latitude'],
                        $updateData['longitude'],
                        $updateData['lokasi_gps'] ?? null
                    );
                }
            }

            Presensi::updateOrCreate(
                ['karyawan_id' => $karyawanId, 'tanggal' => now()->format('Y-m-d')],
                $updateData
            );
        }

        return redirect()->route('presensi.input', $isAdmin ? ['mandor_id' => $mandorId] : [])
            ->with('success', 'Presensi tim berhasil disimpan');
    }

    public function log(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        $query = Presensi::with(['karyawan', 'mandor'])->orderBy('tanggal', 'desc');

        if (!$isAdmin) {
            $query->whereHas('karyawan', fn($q) => $q->where('mandor_id', $user->id));
        }

        if ($request->cari) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->cari.'%')
                  ->orWhere('nik', 'like', '%'.$request->cari.'%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->cari) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->cari.'%')
                  ->orWhere('nik', 'like', '%'.$request->cari.'%');
            });
        }

        if ($request->lokasi) {
            $query->whereHas('karyawan', fn($q) => $q->where('lokasi', $request->lokasi));
        }

        $data = $query->paginate(15)->withQueryString();

        $lokasiQuery = Karyawan::query();
        if (!$isAdmin) {
            $lokasiQuery->where('mandor_id', $user->id);
        }
        $lokasiList = $lokasiQuery->select('lokasi')->distinct()->pluck('lokasi');

        return view('presensi.log', compact('user', 'data', 'lokasiList'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasPermission('data.view_all');

        $query = Presensi::with(['karyawan', 'mandor'])->orderBy('tanggal', 'desc');

        if (!$isAdmin) {
            $query->whereHas('karyawan', fn($q) => $q->where('mandor_id', $user->id));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->lokasi) {
            $query->whereHas('karyawan', fn($q) => $q->where('lokasi', $request->lokasi));
        }

        $data = $query->get();

        $filename = 'laporan-presensi-'.now()->format('Ymd-His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'NIK', 'Nama', 'Lokasi', 'Status', 'Jam Masuk', 'Lokasi GPS', 'Mandor', 'Keterangan']);

            foreach ($data as $p) {
                fputcsv($file, [
                    $p->tanggal->format('Y-m-d'),
                    $p->karyawan->nik ?? '-',
                    $p->karyawan->nama ?? '-',
                    $p->karyawan->lokasi ?? '-',
                    $p->status,
                    $p->jam_masuk ?? '-',
                    $p->lokasi_gps ?? '-',
                    $p->mandor->name ?? '-',
                    $p->keterangan ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tempel info GPS & nama lokasi ke pojok bawah foto presensi.
     */
    private function tempelWatermarkGps(string $path, $lat, $lng, ?string $namaLokasi = null): void
    {
        if (!extension_loaded('gd') || !file_exists($path)) {
            return;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $image = match ($ext) {
            'png' => imagecreatefrompng($path),
            'gif' => imagecreatefromgif($path),
            default => imagecreatefromjpeg($path),
        };

        if (!$image) {
            return;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $boxHeight = $namaLokasi ? 70 : 55;

        $overlay = imagecolorallocatealpha($image, 0, 0, 0, 55);
        imagefilledrectangle($image, 0, $height - $boxHeight, $width, $height, $overlay);

        $putih = imagecolorallocate($image, 255, 255, 255);
        $y = $height - $boxHeight + 8;

        if ($namaLokasi) {
            $lokasiTampil = strlen($namaLokasi) > 60 ? substr($namaLokasi, 0, 57).'...' : $namaLokasi;
            imagestring($image, 4, 10, $y, $lokasiTampil, $putih);
            $y += 20;
        }

        imagestring($image, 3, 10, $y, 'Lat: '.number_format((float) $lat, 6).', Lng: '.number_format((float) $lng, 6), $putih);
        $y += 15;
        imagestring($image, 3, 10, $y, now()->format('d/m/Y H:i:s'), $putih);

        match ($ext) {
            'png' => imagepng($image, $path),
            'gif' => imagegif($image, $path),
            default => imagejpeg($image, $path, 90),
        };

        imagedestroy($image);
    }
}