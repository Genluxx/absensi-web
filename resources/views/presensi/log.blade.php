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
            <a href="{{ route('presensi.log.export', request()->query()) }}"
    class="flex items-center gap-1.5 bg-gray-900 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Export Excel/CSV
            </a>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Ketik nama atau NIK..."
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
                <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Alpa" {{ request('status') == 'Alpa' ? 'selected' : '' }}>Alpa</option>
            </select>
            <button class="md:col-span-3 bg-gray-100 rounded-lg py-2 text-sm font-bold">Filter</button>
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
                    <th class="pb-2">Status</th>
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
                    <td class="py-3">
                        <span class="px-2 py-1 rounded text-xs font-bold
                            @if($item->status == 'Hadir') bg-green-100 text-green-700
                            @elseif($item->status == 'Telat') bg-orange-100 text-orange-700
                            @elseif(in_array($item->status, ['Izin','Sakit'])) bg-blue-100 text-blue-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $item->status }}
                        </span>
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
                    <td class="py-3 text-xs">{{ $item->keterangan ?? '-' }}</td>
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