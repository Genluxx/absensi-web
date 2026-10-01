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

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="glass-panel rounded-xl p-4"><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Total role</p><p class="mt-2 text-2xl font-extrabold text-white">{{ $roles->count() }}</p></div>
            <div class="glass-panel rounded-xl p-4"><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Akun terhubung</p><p class="mt-2 text-2xl font-extrabold text-cyan-300">{{ $roles->sum('users_count') }}</p></div>
            <div class="glass-panel rounded-xl p-4"><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Permission tersedia</p><p class="mt-2 text-2xl font-extrabold text-emerald-300">{{ $allPermissions->flatten()->count() }}</p></div>
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
                            @if ($role->description)
                                <p class="text-sm text-slate-400 mt-1">{{ $role->description }}</p>
                            @endif
                            <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                                <span class="rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-blue-700">{{ $role->users_count }} akun</span>
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700">{{ $role->permissions_count }} permission</span>
                            </div>
                        </div>
                        @if (!$role->is_system)
                            <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Hapus role ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-action inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-red-600 text-xs font-bold hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-1.327L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-10.15 0a48.11 48.11 0 013.478-.397m7.172 0V4.5A2.25 2.25 0 0014 2.25h-4.5A2.25 2.25 0 007.25 4.5v.893m7.172 0a48.11 48.11 0 00-7.172 0" /></svg>
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('roles.update', $role) }}" class="mt-5 border-t border-slate-200/70 pt-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $role->name }}">
                        <input type="hidden" name="description" value="{{ $role->description }}">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Permission aktif</p>
                        <div class="grid max-h-48 grid-cols-1 gap-2 overflow-y-auto rounded-xl border border-slate-200 bg-white/60 p-3 sm:grid-cols-2">
                            @foreach ($allPermissions as $group => $permissions)
                                @foreach ($permissions as $permission)
                                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-slate-400 text-blue-600" {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}>
                                        {{ $permission->name }}
                                    </label>
                                @endforeach
                            @endforeach
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <p class="text-xs text-slate-500">Akun dengan role ini hanya bisa mengakses permission yang dicentang.</p>
                            <button type="submit" class="shrink-0 rounded-lg bg-cyan-400 px-3 py-2 text-xs font-bold text-slate-950 hover:bg-cyan-300">Sinkronkan akses</button>
                        </div>
                    </form>
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
