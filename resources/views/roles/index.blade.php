@extends('layouts.app')

@section('title', 'Role & Akses - SAP.HRIS')
@section('page-title', 'Role & Akses')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-300 mb-2">Access governance</p>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white">Role & hak akses</h1>
                <p class="text-sm text-slate-400 mt-2">Atur akses berbasis tanggung jawab tanpa membuka data di luar kebutuhan kerja.</p>
            </div>
            <button type="button" onclick="document.getElementById('modalRole').classList.remove('hidden')" class="glow-button rounded-xl bg-cyan-400 px-4 py-2.5 text-sm font-bold text-slate-950 hover:bg-cyan-300">Tambah role</button>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            @forelse ($roles as $role)
                <div class="glass-panel rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-bold text-white">{{ $role->name }}</h2>
                                @if ($role->is_system)
                                    <span class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-2 py-1 text-[10px] font-bold text-cyan-200">SYSTEM</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-400 mt-1">{{ $role->description ?: 'Belum ada deskripsi role.' }}</p>
                        </div>
                        @if (!$role->is_system)
                            <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Hapus role ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-rose-300 hover:text-rose-200">Hapus</button>
                            </form>
                        @endif
                    </div>
                    <div class="mt-5 flex flex-wrap gap-2">
                        @forelse ($role->permissions as $permission)
                            <span class="rounded-lg border border-slate-700 bg-slate-950/40 px-2.5 py-1.5 text-xs text-slate-300">{{ $permission->name }}</span>
                        @empty
                            <span class="text-xs text-slate-500">Belum ada permission.</span>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="glass-panel rounded-2xl p-8 text-center text-slate-400 xl:col-span-2">Belum ada role yang tersedia.</div>
            @endforelse
        </div>
    </div>

    <div id="modalRole" class="hidden fixed inset-0 z-50 bg-black/60 p-4 sm:p-6 flex items-center justify-center">
        <div class="glass-panel max-w-xl w-full rounded-2xl p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-white">Tambah role baru</h2>
                    <p class="text-sm text-slate-400 mt-1">Gunakan nama yang mencerminkan fungsi kerja.</p>
                </div>
                <button type="button" onclick="document.getElementById('modalRole').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <form method="POST" action="{{ route('roles.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Nama role</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-sm text-white">{{ old('description') }}</textarea>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-300 mb-2">Permission</p>
                    <div class="max-h-48 overflow-y-auto space-y-3 rounded-xl border border-slate-700 bg-slate-950/30 p-3">
                        @foreach ($allPermissions as $group => $permissions)
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-300 mb-2">{{ $group }}</p>
                                <div class="grid sm:grid-cols-2 gap-2">
                                    @foreach ($permissions as $permission)
                                        <label class="flex items-center gap-2 text-xs text-slate-300">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-slate-600 bg-slate-900 text-cyan-400">
                                            {{ $permission->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalRole').classList.add('hidden')" class="flex-1 rounded-xl border border-slate-700 py-2.5 text-sm font-bold text-slate-300">Batal</button>
                    <button type="submit" class="flex-1 rounded-xl bg-cyan-400 py-2.5 text-sm font-bold text-slate-950 hover:bg-cyan-300">Simpan role</button>
                </div>
            </form>
        </div>
    </div>
@endsection
