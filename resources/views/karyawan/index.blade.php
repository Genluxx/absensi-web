@extends('layouts.app')

@section('title', 'Kelola Karyawan - SAP.HRIS')
@section('page-title', 'Kelola Karyawan')

@section('content')
    <div class="bg-slate-900/80 rounded-2xl shadow-sm border border-slate-700 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="font-bold text-lg text-white">Daftar Karyawan</h2>
                <p class="text-sm text-slate-400">
                    {{ $isAdmin ? 'Seluruh karyawan dari semua tim' : 'Karyawan di tim Anda' }}
                </p>
            </div>
            @if ($isAdmin)
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                    class="glow-button flex items-center gap-1.5 bg-blue-500/10 text-blue-300 border border-blue-400/30 text-sm font-bold px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Karyawan
            </button>
            @endif
        </div>

        <form method="GET" class="flex gap-3 mb-6">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau NIK..."
                   class="border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button class="bg-slate-800 text-slate-100 rounded-lg px-4 text-sm font-bold border border-slate-700">Cari</button>
        </form>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                    <th class="pb-2">NIK</th>
                    <th class="pb-2">Nama</th>
                    <th class="pb-2">Jabatan</th>
                    <th class="pb-2">Tipe</th>
                    <th class="pb-2">Lokasi</th>
                    <th class="pb-2">Akun</th>
                    @if ($isAdmin)
                        <th class="pb-2">Aksi</th>
                        <th class="pb-2">Mandor</th>
                    @endif
                    <th class="pb-2">Absensi Hari Ini</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $k)
                <tr class="border-b border-slate-800">
                    <td class="py-3 text-slate-200">{{ $k->nik }}</td>
                    <td class="py-3 font-bold text-white">{{ $k->nama }}</td>
                    <td class="py-3 text-slate-300">{{ $k->jabatan ?? '-' }}</td>
                    <td class="py-3">
                        <span class="bg-slate-800 text-slate-200 text-xs px-2 py-1 rounded">{{ $k->tipe === 'kebun' ? 'Kebun' : 'Pabrik' }}</span>
                    </td>
                    <td class="py-3 text-slate-300">{{ $k->lokasi }}</td>
                    <td class="py-3 text-xs {{ $k->user_id ? 'text-emerald-300' : 'text-amber-300' }}">
                        {{ $k->user_id ? 'Aktif' : 'Belum dibuat' }}
                    </td>
                    @if ($isAdmin)
                        <td class="py-3">
                            @if (!$k->user_id)
                                <button type="button"
                                        onclick="bukaAkun({{ $k->id }}, '{{ addslashes($k->nama) }}')"
                                        class="text-cyan-300 text-xs font-bold hover:text-cyan-200">
                                    Buat Akun
                                </button>
                            @else
                                <span class="text-slate-500 text-xs">Tersedia</span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-300">{{ $k->mandor->name ?? '-' }}</td>
                    @endif
                    @php($presensiHariIni = $k->presensi->first())
                    <td class="py-3">
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach (['Hadir', 'Izin', 'Cuti', 'Sakit'] as $status)
                                <form method="POST" action="{{ route('karyawan.presensi.store', $k->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $status }}">
                                    <button type="submit"
                                            class="rounded-lg px-2 py-1 text-[11px] font-bold transition {{ $presensiHariIni && in_array($presensiHariIni->status, [$status, $status === 'Hadir' ? 'Telat' : $status], true) ? 'bg-cyan-500 text-slate-950' : 'border border-slate-600 text-slate-300 hover:border-cyan-400 hover:text-cyan-300' }}">
                                        {{ $status }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                        @if ($presensiHariIni)
                            <p class="mt-1 text-[11px] text-slate-400">
                                {{ $presensiHariIni->status }}{{ $presensiHariIni->jam_masuk ? ' · '.$presensiHariIni->jam_masuk : '' }}
                            </p>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isAdmin ? 9 : 7 }}" class="py-6 text-center text-slate-400">Belum ada data karyawan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4 text-slate-300">
            {{ $karyawan->links() }}
        </div>
    </div>

    @if ($isAdmin)
    <!-- Modal Akun Karyawan -->
    <div id="modalAkun" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center p-6">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full">
            <h3 class="font-bold text-lg mb-1 text-white">Buat Akun Karyawan</h3>
            <p class="text-sm text-slate-400 mb-4">Nama: <span id="namaAkun" class="text-cyan-300 font-bold"></span></p>
            <form method="POST" id="formAkun">
                @csrf
                <label class="block text-xs font-bold mb-1 text-slate-300">Username</label>
                <input type="text" name="username" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Email</label>
                <input type="email" name="email" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Password</label>
                <input type="password" name="password" minlength="6" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4" required>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('modalAkun').classList.add('hidden')"
                            class="flex-1 border border-slate-600 rounded-xl py-2 font-bold text-slate-200">Batal</button>
                    <button type="submit" class="flex-1 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl py-2 font-bold">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah -->
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
                <p class="text-xs font-bold uppercase tracking-wide text-cyan-300 mb-2">Akun Login Karyawan</p>
                <label class="block text-xs font-bold mb-1 text-slate-300">Username</label>
                <input type="text" name="username" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Email</label>
                <input type="email" name="email" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Password</label>
                <input type="password" name="password" minlength="6" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4" required>
                @if ($isAdmin)
                    <label class="block text-xs font-bold mb-1 text-slate-300">Tim mandor</label>
                    <select name="mandor_id" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4" required>
                        <option value="">Pilih tim...</option>
                        @foreach ($mandorList as $mandor)
                            <option value="{{ $mandor->id }}">{{ $mandor->name }}{{ $mandor->area ? ' - '.$mandor->area : '' }}</option>
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
    @endif

    <!-- Modal Edit -->
    <div id="modalEdit" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center p-6">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full">
            <h3 class="font-bold text-lg mb-4 text-white">Edit Karyawan</h3>
            <form method="POST" id="formEdit">
                @csrf
                @method('PUT')
                <label class="block text-xs font-bold mb-1 text-slate-300">NIK</label>
                <input type="text" name="nik" id="editNik" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Nama Lengkap</label>
                <input type="text" name="nama" id="editNama" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Jabatan (opsional)</label>
                <input type="text" name="jabatan" id="editJabatan" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4">
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                            class="flex-1 border border-slate-600 rounded-xl py-2 font-bold text-slate-200">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white rounded-xl py-2 font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaEdit(id, nik, nama, jabatan) {
            document.getElementById('formEdit').action = '/karyawan/' + id;
            document.getElementById('editNik').value = nik;
            document.getElementById('editNama').value = nama;
            document.getElementById('editJabatan').value = jabatan;
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        function bukaAkun(id, nama) {
            document.getElementById('formAkun').action = '/karyawan/' + id + '/akun';
            document.getElementById('namaAkun').textContent = nama;
            document.getElementById('modalAkun').classList.remove('hidden');
        }
    </script>
@endsection