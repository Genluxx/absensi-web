@extends('layouts.app')

@section('title', 'Log & Laporan - SAP.HRIS')
@section('page-title', 'Log & Laporan Presensi')

@section('content')
    <div class="bg-white text-slate-800 rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="font-bold text-lg">Log & Histori Presensi</h2>
                <p class="text-sm text-gray-500">Filter cepat, pencarian NIK</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
                <div class="rounded-xl bg-slate-100 p-3"><p class="text-xs text-gray-500">Wajib absen</p><p class="text-xl font-extrabold">{{ $ringkasan['wajibAbsen'] }}</p></div>
                <div class="rounded-xl bg-emerald-50 p-3"><p class="text-xs text-gray-500">Tepat waktu ≤ 08:00</p><p class="text-xl font-extrabold text-emerald-700">{{ $ringkasan['tepatWaktu'] }}</p></div>
                <div class="rounded-xl bg-orange-50 p-3"><p class="text-xs text-gray-500">Terlambat &gt; 08:00</p><p class="text-xl font-extrabold text-orange-700">{{ $ringkasan['terlambat'] }}</p></div>
                <div class="rounded-xl bg-blue-50 p-3"><p class="text-xs text-gray-500">Cuti</p><p class="text-xl font-extrabold text-blue-700">{{ $ringkasan['cuti'] }}</p></div>
                <div class="rounded-xl bg-indigo-50 p-3"><p class="text-xs text-gray-500">Izin / sakit</p><p class="text-xl font-extrabold text-indigo-700">{{ $ringkasan['izinSakit'] }}</p></div>
            </div>
            <a href="{{ route('presensi.log.export', request()->query()) }}"
    class="export-action flex items-center gap-1.5 bg-gray-900 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Export Excel/CSV
            </a>
        </div>

         <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Ketik nama atau NIK..."
                   class="border rounded-lg px-3 py-2 text-sm">
             <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" aria-label="Tanggal mulai"
                 class="border rounded-lg px-3 py-2 text-sm">
             <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" aria-label="Tanggal selesai"
                 class="border rounded-lg px-3 py-2 text-sm">
            <select name="lokasi" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Divisi / Stasiun</option>
                @foreach ($lokasiList as $lokasi)
                    <option value="{{ $lokasi }}" {{ request('lokasi') == $lokasi ? 'selected' : '' }}>{{ $lokasi }}</option>
                @endforeach
            </select>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="Telat" {{ request('status') == 'Telat' ? 'selected' : '' }}>Telat</option>
                <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Alpa" {{ request('status') == 'Alpa' ? 'selected' : '' }}>Alpa</option>
            </select>
            <button class="md:col-span-5 bg-gray-100 rounded-lg py-2 text-sm font-bold">Terapkan filter laporan</button>
        </form>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full min-w-[980px] text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 border-b">
                    <th class="pb-2">Absen ID</th>
                    <th class="pb-2">Tanggal</th>
                    <th class="pb-2">Nama Karyawan</th>
                    <th class="pb-2">Divisi/Blok</th>
                    <th class="pb-2">Jam Masuk</th>
                    <th class="pb-2">Waktu Dicatat</th>
                    <th class="pb-2">Status</th>
                       <th class="pb-2">Lokasi saat absen</th>
                    <th class="pb-2">Bukti</th>
                    <th class="pb-2">Keterangan</th>
                    <th class="pb-2">Mandor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr class="border-b">
                    <td class="py-3 text-xs">ABS-{{ $item->tanggal->format('Ymd') }}-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="py-3">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td class="py-3 font-bold">{{ $item->karyawan->nama }}</td>
                    <td class="py-3">{{ $item->karyawan->lokasi }}</td>
                    <td class="py-3">{{ $item->jam_masuk ?? '-' }}</td>
                    <td class="py-3 text-xs">{{ $item->created_at?->format('d/m/Y H:i:s') ?? '-' }}</td>
                    <td class="py-3">
                        <span class="px-2 py-1 rounded text-xs font-bold
                            @if($item->status == 'Hadir') bg-green-100 text-green-700
                            @elseif($item->status == 'Telat') bg-orange-100 text-orange-700
                            @elseif($item->status === 'Cuti') bg-purple-100 text-purple-700
                            @elseif(in_array($item->status, ['Izin','Sakit'])) bg-blue-100 text-blue-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $item->status }}{{ $item->terlambat ? ' - terlambat' : '' }}
                        </span>
                    </td>
                        <td class="py-3">
                            @if(is_numeric($item->latitude) && is_numeric($item->longitude))
                                <div class="flex flex-col items-start gap-1">
                                    <span class="font-mono text-[11px] tabular-nums text-slate-500">{{ number_format((float) $item->latitude, 6) }}, {{ number_format((float) $item->longitude, 6) }}</span>
                                    <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($item->latitude.','.$item->longitude) }}"
                                       target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1 rounded-md border border-cyan-200 bg-cyan-50 px-2 py-1 text-xs font-bold text-cyan-800 transition hover:bg-cyan-100 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                       aria-label="Lihat lokasi presensi {{ $item->karyawan->nama }} pada {{ $item->tanggal->format('d/m/Y') }} di Google Maps">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.686 7-12a7 7 0 10-14 0c0 6.314 7 12 7 12z" />
                                            <circle cx="12" cy="9" r="2.25" />
                                        </svg>
                                        Lihat peta
                                    </a>
                                </div>
                            @else
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-xs text-slate-500">GPS tidak direkam</span>
                            @endif
                        </td>
                    <td class="py-3">
                        @if($item->foto_path)
                            <button onclick="document.getElementById('modalFoto{{ $item->id }}').classList.remove('hidden')"
                                    class="flex items-center gap-1 border rounded-lg px-2 py-1 text-xs font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.174C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-4.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                                Lihat
                            </button>
                            <div id="modalFoto{{ $item->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center p-6 z-50">
                                <div class="bg-white rounded-2xl p-4 max-w-sm w-full">
                                    <img src="{{ asset('storage/' . $item->foto_path) }}" class="w-full rounded-lg mb-2">
                                    @if($item->latitude)
                                        <p class="text-xs text-gray-500">GPS: {{ $item->latitude }}, {{ $item->longitude }}</p>
                                    @endif
                                    <button onclick="document.getElementById('modalFoto{{ $item->id }}').classList.add('hidden')"
                                            class="w-full bg-gray-100 rounded-lg py-2 text-sm font-bold mt-2">Tutup</button>
                                </div>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-3 text-xs">{{ $item->keterangan ?? ($item->terlambat ? 'Terlambat absen setelah 08:00' : '-') }}</td>
                    <td class="py-3">{{ $item->mandor->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <div class="mt-4">
            {{ $data->links() }}
        </div>
    </div>
@endsection