@extends('layouts.app')

@section('title', 'Kelola Mandor - SAP.HRIS')
@section('page-title', 'Kelola Mandor')

@section('content')
    <div class="bg-slate-900/80 rounded-2xl shadow-sm border border-slate-700 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="font-bold text-lg text-white">Akun Operasional</h2>
                <p class="text-sm text-slate-400">Kelola akun Mandor & role custom lainnya</p>
            </div>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                    class="glow-button flex items-center gap-1.5 bg-blue-500/10 text-blue-300 border border-blue-400/30 text-sm font-bold px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Akun
            </button>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                    <th class="pb-2">Nama</th>
                    <th class="pb-2">Username</th>
                    <th class="pb-2">Role</th>
                    <th class="pb-2">Area</th>
                    <th class="pb-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mandorList as $m)
                <tr class="border-b border-slate-800">
                    <td class="py-3 font-bold text-white">{{ $m->name }}</td>
                    <td class="py-3 text-slate-300">{{ $m->username }}</td>
                    <td class="py-3">
                        <span class="bg-slate-800 text-slate-200 text-xs px-2 py-1 rounded">{{ ucwords(str_replace('_', ' ', $m->role)) }}</span>
                    </td>
                    <td class="py-3 text-slate-300">{{ $m->area ?? '-' }}</td>
                    <td class="py-3">
                        <form method="POST" action="{{ route('mandor.destroy', $m->id) }}"
                              onsubmit="return confirm('Yakin hapus akun {{ $m->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-action inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-red-600 text-xs font-bold hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-1.327L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-10.15 0a48.11 48.11 0 013.478-.397m7.172 0V4.5A2.25 2.25 0 0014 2.25h-4.5A2.25 2.25 0 007.25 4.5v.893m7.172 0a48.11 48.11 0 00-7.172 0" /></svg>
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-slate-400">Belum ada akun.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4 text-slate-300">
            {{ $mandorList->links() }}
        </div>
    </div>

    <!-- Modal Tambah Akun -->
    <div id="modalTambah" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center p-6">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full">
            <h3 class="font-bold text-lg mb-4 text-white">Tambah Akun Baru</h3>
            <form method="POST" action="{{ route('mandor.store') }}">
                @csrf
                <label class="block text-xs font-bold mb-1 text-slate-300">Nama Lengkap</label>
                <input type="text" name="name" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>

                <label class="block text-xs font-bold mb-1 text-slate-300">Username</label>
                <input type="text" name="username" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>

                <label class="block text-xs font-bold mb-1 text-slate-300">Email</label>
                <input type="email" name="email" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>

                <label class="block text-xs font-bold mb-1 text-slate-300">Password awal</label>
                <div class="relative mb-3">
                    <input id="mandorPassword" type="password" name="password" minlength="8" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 pr-20" required>
                    <button type="button" onclick="togglePassword('mandorPassword', this)" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md px-2 py-1 text-xs font-bold text-blue-600 hover:bg-blue-50">Lihat</button>
                </div>
                <p class="-mt-1 mb-3 text-xs text-slate-500">Password disimpan aman dan tidak dapat dilihat kembali setelah akun dibuat.</p>

                <label class="block text-xs font-bold mb-1 text-slate-300">Role</label>
                <select name="role_id" id="roleSelect" onchange="toggleAreaOptions()" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                    <option value="">- Pilih Role -</option>
                    @foreach ($assignableRoles as $role)
                        <option value="{{ $role->id }}" data-slug="{{ $role->slug }}">{{ $role->name }}</option>
                    @endforeach
                </select>

                <label class="block text-xs font-bold mb-1 text-slate-300">Area / Wilayah Kerja</label>

                <select name="area" id="areaKebun" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4 hidden" disabled>
                    <option value="">- Pilih Divisi Kebun -</option>
                    <option value="Divisi I">Divisi I</option>
                    <option value="Divisi II">Divisi II</option>
                    <option value="Divisi III">Divisi III</option>
                    <option value="Divisi IV">Divisi IV</option>
                    <option value="Divisi V">Divisi V</option>
                </select>

                <select name="area" id="areaPabrik" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4 hidden" disabled>
                    <option value="">- Pilih Stasiun Pabrik -</option>
                    <option value="Stasiun Penerimaan TBS">Stasiun Penerimaan TBS</option>
                    <option value="Stasiun Rebusan (Sterilizer)">Stasiun Rebusan (Sterilizer)</option>
                    <option value="Stasiun Penebah (Thresher)">Stasiun Penebah (Thresher)</option>
                    <option value="Stasiun Pengempaan (Pressing)">Stasiun Pengempaan (Pressing)</option>
                    <option value="Stasiun Klarifikasi">Stasiun Klarifikasi</option>
                    <option value="Stasiun Kernel">Stasiun Kernel</option>
                    <option value="Stasiun Boiler">Stasiun Boiler</option>
                    <option value="Stasiun Water Treatment">Stasiun Water Treatment</option>
                </select>

                <input type="text" name="area" id="areaUmum" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4 hidden"
                       placeholder="Contoh: Kantor Pusat, Gudang, dll">

                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                            class="flex-1 border border-slate-600 rounded-xl py-2 font-bold text-slate-200">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white rounded-xl py-2 font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.textContent = visible ? 'Lihat' : 'Sembunyikan';
        }

        function toggleAreaOptions() {
            const select = document.getElementById('roleSelect');
            const slug = select.options[select.selectedIndex]?.dataset.slug;

            const areaKebun = document.getElementById('areaKebun');
            const areaPabrik = document.getElementById('areaPabrik');
            const areaUmum = document.getElementById('areaUmum');

            [areaKebun, areaPabrik, areaUmum].forEach(el => {
                el.classList.add('hidden');
                el.disabled = true;
                el.name = '';
            });

            if (slug === 'mandor_kebun') {
                areaKebun.classList.remove('hidden');
                areaKebun.disabled = false;
                areaKebun.name = 'area';
            } else if (slug === 'mandor_pabrik') {
                areaPabrik.classList.remove('hidden');
                areaPabrik.disabled = false;
                areaPabrik.name = 'area';
            } else if (slug) {
                areaUmum.classList.remove('hidden');
                areaUmum.disabled = false;
                areaUmum.name = 'area';
            }
        }
    </script>
@endsection