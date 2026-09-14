@extends('layouts.app')

@section('title', 'Input Presensi - SAP.HRIS')
@section('page-title', 'Input Presensi Tim')

@section('content')
    <div class="glass-panel rounded-2xl p-4 sm:p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="font-bold text-lg text-white">Form Input Presensi Tim Operasional</h2>
                <p class="text-sm text-slate-400">Pencatatan presensi kolektif mandor</p>
            </div>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                    class="glow-button flex items-center gap-1.5 bg-blue-500/10 text-blue-300 border border-blue-400/30 text-sm font-bold px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Karyawan
            </button>
        </div>

        @if ($isAdmin)
        <form method="GET" action="{{ route('presensi.input') }}" class="mb-4 rounded-xl border border-slate-700 bg-slate-950/50 p-4">
            <label for="mandor_id" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Pilih tim untuk dicatat</label>
            <div class="flex flex-col sm:flex-row gap-3">
                <select id="mandor_id" name="mandor_id" onchange="this.form.submit()" class="flex-1 border border-slate-700 bg-slate-900 text-white rounded-lg px-3 py-2 text-sm">
                    <option value="">Pilih mandor / area...</option>
                    @foreach ($mandorList as $mandor)
                        <option value="{{ $mandor->id }}" {{ (string) $mandorId === (string) $mandor->id ? 'selected' : '' }}>{{ $mandor->name }}{{ $mandor->area ? ' - '.$mandor->area : '' }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-slate-500 self-center">{{ $karyawan->count() }} anggota ditemukan</span>
            </div>
        </form>
        @endif

        <div id="gpsStatus" class="flex items-center gap-2 text-sm rounded-lg p-3 mb-4 bg-amber-500/10 border border-amber-500/30 text-amber-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span id="gpsStatusText">Mendeteksi lokasi GPS...</span>
        </div>

        <form method="POST" action="{{ route('presensi.simpan') }}" enctype="multipart/form-data" id="formPresensi">
            @csrf
            <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full min-w-[920px] text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="pb-2">Karyawan Tim</th>
                        <th class="pb-2">Lokasi/Divisi</th>
                        <th class="pb-2">Status Presensi</th>
                        <th class="pb-2">Foto</th>
                        <th class="pb-2">Keterangan / Hasil Panen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawan as $k)
                    <tr class="border-b border-slate-800">
                        <td class="py-3">
                            <p class="font-bold text-white">{{ $k->nama }}</p>
                            <p class="text-xs text-slate-400">{{ $k->nik }} • {{ $k->jabatan }}</p>
                        </td>
                        <td class="py-3 text-slate-300">
                            <span class="bg-slate-800 text-slate-200 text-xs px-2 py-1 rounded">{{ $k->tipe === 'kebun' ? 'Kebun' : 'Pabrik' }}</span>
                            <span class="ml-2">{{ $k->lokasi }}</span>
                        </td>
                        <td class="py-3">
                            <select name="presensi[{{ $k->id }}][status]" class="border border-slate-700 bg-slate-950 text-white rounded-lg px-2 py-1 text-sm">
                                <option value="">- Pilih -</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Telat">Telat</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpa">Alpa</option>
                            </select>
                        </td>
                        <td class="py-3">
                            <input type="file" name="presensi[{{ $k->id }}][foto]" accept="image/*" capture="environment"
                                   class="text-xs w-32 text-slate-200 file:mr-3 file:rounded file:border-0 file:bg-blue-600 file:text-white file:px-2 file:py-1">
                        </td>
                        <td class="py-3">
                            <input type="text" name="presensi[{{ $k->id }}][keterangan]"
                                   class="border border-slate-700 bg-slate-950 text-white rounded-lg px-2 py-1 text-sm w-full placeholder:text-slate-500"
                                   placeholder="Catatan/Hasil Panen...">
                            <input type="hidden" name="presensi[{{ $k->id }}][latitude]" class="js-lat">
                            <input type="hidden" name="presensi[{{ $k->id }}][longitude]" class="js-lng">
                            <input type="hidden" name="presensi[{{ $k->id }}][lokasi_gps]" class="js-location">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" id="btnSimpan" class="glow-button flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-2 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Simpan Presensi Tim
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Tambah Karyawan -->
    <div id="modalTambah" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center p-6">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full">
            <h3 class="font-bold text-lg mb-4 text-white">Tambah Karyawan Baru</h3>
            <form method="POST" action="{{ route('karyawan.store') }}">
                @csrf
                <label class="block text-xs font-bold mb-1 text-slate-300">NIK</label>
                <input type="text" name="nik" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Nama Lengkap</label>
                <input type="text" name="nama" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Jabatan (opsional)</label>
                <input type="text" name="jabatan" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4">
                @if ($isAdmin)
                    <label class="block text-xs font-bold mb-1 text-slate-300">Tim mandor</label>
                    <select name="mandor_id" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4" required>
                        <option value="">Pilih tim...</option>
                        @foreach ($mandorList as $mandor)
                            <option value="{{ $mandor->id }}" {{ (string) $mandorId === (string) $mandor->id ? 'selected' : '' }}>{{ $mandor->name }}{{ $mandor->area ? ' - '.$mandor->area : '' }}</option>
                        @endforeach
                    </select>
                @endif
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                            class="flex-1 border border-slate-600 rounded-xl py-2 font-bold text-slate-200">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white rounded-xl py-2 font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const btnSimpan = document.getElementById('btnSimpan');
        const gpsStatus = document.getElementById('gpsStatus');
        const gpsStatusText = document.getElementById('gpsStatusText');

        const setGpsState = (message, state) => {
            const colors = state === 'ready'
                ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300'
                : 'bg-amber-500/10 border-amber-500/30 text-amber-200';
            gpsStatus.className = `flex items-center gap-2 text-sm rounded-lg p-3 mb-4 border ${colors}`;
            gpsStatusText.textContent = message;
        };

        if (!navigator.geolocation) {
            setGpsState('Browser tidak mendukung lokasi. Presensi tetap bisa disimpan tanpa GPS.', 'optional');
        } else {
            navigator.geolocation.getCurrentPosition((position) => {
                const { latitude, longitude } = position.coords;
                document.querySelectorAll('.js-lat').forEach((input) => input.value = latitude);
                document.querySelectorAll('.js-lng').forEach((input) => input.value = longitude);
                document.querySelectorAll('.js-location').forEach((input) => input.value = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`);
                setGpsState(`Lokasi aktif: ${latitude.toFixed(5)}, ${longitude.toFixed(5)}`, 'ready');
            }, () => {
                setGpsState('Lokasi tidak diizinkan. Presensi tetap bisa disimpan tanpa GPS.', 'optional');
            }, { enableHighAccuracy: true, timeout: 8000, maximumAge: 300000 });
        }

        btnSimpan.disabled = false;
    </script>
@endsection