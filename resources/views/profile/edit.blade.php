@extends('layouts.app')

@section('title', 'Profil Saya - SAP.HRIS')
@section('page-title', 'Profil Saya')

@section('content')
    <div class="max-w-2xl">
        <div class="glass-panel rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-16 h-16 bg-gradient-to-br from-[#69d2e7] to-[#397bb1] rounded-full flex items-center justify-center text-[#082238] font-extrabold text-2xl">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <p class="font-bold text-lg text-white">{{ $user->name }}</p>
                    <p class="text-sm text-slate-400">{{ $user->username }} • {{ ucwords(str_replace('_', ' ', $user->role)) }}</p>
                    @if ($user->area)
                        <p class="text-xs text-slate-500 mt-1">Area kerja · {{ $user->area }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-6">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-300 mb-2">Security</p>
            <h2 class="font-bold text-lg text-white mb-1">Perbarui password</h2>
            <p class="text-sm text-slate-400 mb-5">Gunakan password yang unik untuk menjaga akun operasional tetap aman.</p>

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')

                <label class="block text-xs font-bold text-slate-300 mb-1">Password Lama</label>
                <input type="password" name="password_lama" class="w-full border border-slate-700 bg-slate-950/60 text-white rounded-lg px-3 py-2 mb-3" required>

                <label class="block text-xs font-bold text-slate-300 mb-1">Password Baru</label>
                <input type="password" name="password_baru" class="w-full border border-slate-700 bg-slate-950/60 text-white rounded-lg px-3 py-2 mb-3" required>

                <label class="block text-xs font-bold text-slate-300 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_baru_confirmation" class="w-full border border-slate-700 bg-slate-950/60 text-white rounded-lg px-3 py-2 mb-4" required>

                <button type="submit" class="w-full bg-cyan-400 hover:bg-cyan-300 text-slate-950 font-bold py-2.5 rounded-xl transition">
                    Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
@endsection