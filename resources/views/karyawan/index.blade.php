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
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                    class="glow-button flex items-center gap-1.5 bg-blue-500/10 text-blue-300 border border-blue-400/30 text-sm font-bold px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Karyawan
            </button>
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
                    @if ($isAdmin)
                        <th class="pb-2">Mandor</th>
                    @else
                        <th class="pb-2">Aksi</th>
                    @endif
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
                    @if ($isAdmin)
                        <td class="py-3 text-slate-300">{{ $k->mandor->name ?? '-' }}</td>
                    @else
                        <td class="py-3 flex gap-3">
                            <button onclick="bukaEdit({{ $k->id }}, '{{ $k->nik }}', '{{ addslashes($k->nama) }}', '{{ addslashes($k->jabatan ?? '') }}')"
                                    class="text-blue-300 text-xs font-bold">Edit</button>
                            <form method="POST" action="{{ route('karyawan.destroy', $k->id) }}"
                                  onsubmit="return confirm('Yakin hapus {{ $k->nama }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 text-xs font-bold">Hapus</button>
                            </form>
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-400">Belum ada data karyawan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4 text-slate-300">
            {{ $karyawan->links() }}
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
    </script>
@endsection