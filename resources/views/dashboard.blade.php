@extends('layouts.app')

@section('title', 'Dashboard - SAP.HRIS')
@section('page-title', 'Dashboard')

@section('content')

<div class="w-full max-w-[1600px] mx-auto">

    {{-- ================= HERO ================= --}}
    <section
        class="relative overflow-hidden rounded-xl sm:rounded-2xl
               border border-cyan-300/20 bg-[#123653]
               p-4 sm:p-6 lg:p-8 mb-4 sm:mb-6
               shadow-xl shadow-slate-950/20">

        {{-- Pattern --}}
        <div
            class="pointer-events-none absolute inset-y-0 right-0 hidden sm:block w-1/2 lg:w-2/5 opacity-20"
            style="
                background-image:
                linear-gradient(
                    135deg,
                    transparent 25%,
                    #69d2e7 25%,
                    #69d2e7 26%,
                    transparent 26%,
                    transparent 50%,
                    #69d2e7 50%,
                    #69d2e7 51%,
                    transparent 51%
                );
                background-size: 28px 28px;
            ">
        </div>

        <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">

            <div class="min-w-0">

                <div class="flex items-center gap-2 mb-3 sm:mb-4">
                    <span
                        class="shrink-0 h-2 w-2 rounded-full bg-emerald-300
                               shadow-[0_0_12px_rgba(110,231,183,.7)]">
                    </span>

                    <p
                        class="text-[10px] sm:text-xs font-bold uppercase
                               tracking-[0.16em] sm:tracking-[0.24em]
                               text-cyan-200">
                        Operational overview
                    </p>
                </div>

                <h1
                    class="text-2xl sm:text-3xl lg:text-4xl
                           font-extrabold tracking-tight text-white
                           leading-tight break-words">
                    Selamat datang, {{ $user->name }}
                </h1>

                <p
                    class="mt-2 text-sm sm:text-base text-slate-300
                           max-w-2xl leading-relaxed">
                    Pantau kesiapan tim dan ambil tindakan dari satu ruang kerja.

                    <span class="block sm:inline mt-1 sm:mt-0 text-cyan-100">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </p>

            </div>

            <div
                class="flex flex-col sm:flex-row lg:flex-col
                       sm:items-center lg:items-end
                       gap-2 sm:gap-4 lg:gap-3">

                <span class="text-xs font-semibold text-cyan-100/60">
                    Status sistem
                </span>

                <a
                    href="{{ route('presensi.log') }}"
                    class="glow-button
                           inline-flex w-full sm:w-auto
                           items-center justify-center gap-2
                           rounded-xl bg-white
                           px-4 py-2.5
                           text-sm font-bold text-[#123451]
                           transition
                           hover:bg-cyan-50">

                    Buka laporan

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13.5 4.5L19 10m0 0l-5.5 5.5M19 10H5"
                        />
                    </svg>

                </a>

            </div>

        </div>

    </section>


    {{-- ================= STATISTICS ================= --}}
    <section
        class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5
               gap-3 sm:gap-4 mb-4 sm:mb-6">


        {{-- TOTAL KARYAWAN --}}
        <div
            class="glass-panel min-w-0 rounded-xl sm:rounded-2xl
                   p-4 sm:p-5 border-t-2 border-cyan-300">

            <div class="flex items-center justify-between gap-2">

                <p
                    class="text-[10px] sm:text-xs font-bold uppercase
                           tracking-wide text-slate-400 truncate">
                    Total tim
                </p>

                <span class="text-cyan-300 shrink-0">
                    ◈
                </span>

            </div>

            <p
                class="text-2xl sm:text-3xl font-extrabold
                       text-white mt-3 sm:mt-4">
                {{ $totalKaryawan ?? '-' }}
            </p>

            <p class="text-[11px] sm:text-xs text-slate-500 mt-1.5 sm:mt-2">
                Anggota terdaftar
            </p>

        </div>


        {{-- HADIR --}}
        <div
            class="glass-panel min-w-0 rounded-xl sm:rounded-2xl
                   p-4 sm:p-5 border-t-2 border-emerald-300">

            <div class="flex items-center justify-between gap-2">

                <p
                    class="text-[10px] sm:text-xs font-bold uppercase
                           tracking-wide text-slate-400 truncate">
                    Hadir
                </p>

                <span class="text-emerald-300 shrink-0">
                    ●
                </span>

            </div>

            <p
                class="text-2xl sm:text-3xl font-extrabold
                       text-emerald-300 mt-3 sm:mt-4">
                {{ $hadirHariIni ?? '-' }}
            </p>

            <p class="text-[11px] sm:text-xs text-slate-500 mt-1.5 sm:mt-2">
                Termasuk status telat
            </p>

        </div>


        {{-- IZIN / SAKIT --}}
        <div
            class="glass-panel min-w-0 rounded-xl sm:rounded-2xl
                   p-4 sm:p-5 border-t-2 border-amber-300">

            <div class="flex items-center justify-between gap-2">

                <p
                    class="text-[10px] sm:text-xs font-bold uppercase
                           tracking-wide text-slate-400 truncate">
                    Izin / sakit
                </p>

                <span class="text-amber-300 shrink-0">
                    ●
                </span>

            </div>

            <p
                class="text-2xl sm:text-3xl font-extrabold
                       text-amber-300 mt-3 sm:mt-4">
                {{ $izinSakit ?? '-' }}
            </p>

            <p class="text-[11px] sm:text-xs text-slate-500 mt-1.5 sm:mt-2">
                Perlu tindak lanjut
            </p>

        </div>


        {{-- ALPA --}}
        <div
            class="glass-panel min-w-0 rounded-xl sm:rounded-2xl
                   p-4 sm:p-5 border-t-2 border-rose-300">

            <div class="flex items-center justify-between gap-2">

                <p
                    class="text-[10px] sm:text-xs font-bold uppercase
                           tracking-wide text-slate-400 truncate">
                    Alpa
                </p>

                <span class="text-rose-300 shrink-0">
                    ●
                </span>

            </div>

            <p
                class="text-2xl sm:text-3xl font-extrabold
                       text-rose-300 mt-3 sm:mt-4">
                {{ $alpa ?? '-' }}
            </p>

            <p class="text-[11px] sm:text-xs text-slate-500 mt-1.5 sm:mt-2">
                Perlu perhatian segera
            </p>

        </div>


        {{-- KEHADIRAN --}}
        <div
            class="glass-panel min-w-0 rounded-xl sm:rounded-2xl
                   p-4 sm:p-5 border-t-2 border-blue-300
                   col-span-2 md:col-span-1">

            <div class="flex items-center justify-between gap-2">

                <p
                    class="text-[10px] sm:text-xs font-bold uppercase
                           tracking-wide text-slate-400 truncate">
                    Kehadiran
                </p>

                <span class="text-blue-300 shrink-0">
                    ↗
                </span>

            </div>

            <p
                class="text-2xl sm:text-3xl font-extrabold
                       text-blue-200 mt-3 sm:mt-4">
                {{ $persentaseHadir ?? 0 }}%
            </p>

            <p class="text-[11px] sm:text-xs text-slate-500 mt-1.5 sm:mt-2">
                {{ $belumAbsen ?? 0 }} belum tercatat
            </p>

        </div>

    </section>


    {{-- ================= CHART + ACTION ================= --}}
    <section
        class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_300px]
               gap-4 sm:gap-6">


        {{-- CHART --}}
        <div
            class="glass-panel min-w-0 rounded-xl sm:rounded-2xl
                   p-4 sm:p-5 md:p-6">

            @php
                $periodPresentTotal = collect($grafikHadir ?? [])->sum();
                $periodAbsentTotal = collect($grafikTidakHadir ?? [])->sum();
                $periodAttendanceTotal = $periodPresentTotal + $periodAbsentTotal;
                $periodAttendanceRate = $periodAttendanceTotal > 0
                    ? round(($periodPresentTotal / $periodAttendanceTotal) * 100)
                    : 0;
            @endphp

            <div
                class="flex flex-col sm:flex-row
                       sm:items-start sm:justify-between
                       gap-3 sm:gap-4 mb-5">

                <div class="min-w-0">

                    <h2
                        class="font-bold text-base sm:text-lg
                               text-white leading-tight">
                        Grafik Kehadiran Tim Operasional
                    </h2>

                    <p
                        class="text-xs sm:text-sm
                               text-slate-400 mt-1">
                        Statistik presensi 7 hari terakhir
                    </p>

                </div>

                <span
                    class="self-start inline-flex items-center gap-2
                           rounded-full border border-cyan-400/20
                           bg-cyan-400/10
                           px-3 py-1
                           text-[10px] sm:text-xs
                           text-cyan-200">

                    <span
                        class="h-1.5 w-1.5 rounded-full
                               bg-cyan-300">
                    </span>

                    7 hari

                </span>

            </div>

            <div class="mb-4 flex flex-wrap items-center gap-x-5 gap-y-2 border-y border-slate-200/70 py-3" aria-label="Ringkasan presensi tujuh hari">
                <div class="inline-flex items-center gap-2 text-xs">
                    <span class="h-2 w-2 rounded-full bg-cyan-500" aria-hidden="true"></span>
                    <span class="text-slate-500">Total hadir</span>
                    <strong class="font-extrabold tabular-nums text-cyan-700" data-stat-count="{{ $periodPresentTotal }}">{{ $periodPresentTotal }}</strong>
                </div>
                <div class="inline-flex items-center gap-2 text-xs">
                    <span class="h-2 w-2 rounded-full bg-amber-400" aria-hidden="true"></span>
                    <span class="text-slate-500">Tidak hadir</span>
                    <strong class="font-extrabold tabular-nums text-amber-700" data-stat-count="{{ $periodAbsentTotal }}">{{ $periodAbsentTotal }}</strong>
                </div>
                <div class="ml-auto inline-flex items-baseline gap-1.5 text-xs">
                    <strong class="text-base font-extrabold tabular-nums text-slate-800" data-stat-count="{{ $periodAttendanceRate }}" data-stat-suffix="%">{{ $periodAttendanceRate }}%</strong>
                    <span class="text-slate-500">tingkat kehadiran</span>
                </div>
            </div>


            <div class="w-full min-w-0">
                <div class="mb-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-[11px] font-semibold text-slate-300" aria-label="Legenda grafik">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-cyan-400" aria-hidden="true"></span>Hadir</span>
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-amber-400" aria-hidden="true"></span>Tidak Hadir</span>
                    <span class="ml-auto hidden text-[10px] font-medium text-slate-500 sm:inline">Pilih batang untuk detail</span>
                </div>

                <div id="grafikKehadiranWrap" class="relative h-[260px] w-full min-w-0 overflow-hidden sm:h-[320px] lg:h-[360px]" aria-describedby="grafikKehadiranHelp">
                    <p id="grafikKehadiranHelp" class="sr-only">Grafik batang 2D berkelompok membandingkan jumlah hadir dan tidak hadir dalam tujuh hari terakhir. Gunakan tabel data jika grafik tidak tersedia.</p>
                    <div id="grafikKehadiranAxis" class="pointer-events-none absolute inset-y-2 left-0 z-10 flex w-8 flex-col justify-between text-right text-[9px] tabular-nums text-slate-500" aria-hidden="true"></div>
                    <div class="absolute inset-y-0 left-9 right-0 flex flex-col">
                        <canvas id="grafikKehadiran" class="min-h-0 w-full flex-1 touch-pan-y" aria-label="Grafik batang kehadiran dan ketidakhadiran tujuh hari terakhir" role="img"></canvas>
                        <div id="grafikKehadiranDays" class="grid h-7 shrink-0 grid-cols-7 items-center gap-0 text-center text-[9px] text-slate-400 sm:text-[10px]" aria-hidden="true"></div>
                    </div>
                    <div id="grafikKehadiranTooltip" class="pointer-events-none absolute z-20 hidden rounded-md border border-cyan-300/20 bg-slate-950/95 px-3 py-2 text-xs text-white shadow-xl" role="status" aria-live="polite"></div>
                    <div id="grafikKehadiranFallback" class="absolute inset-0 z-30 hidden overflow-auto rounded-lg border border-slate-700 bg-slate-950 p-4 text-sm text-slate-300" role="status">
                        <p class="font-semibold text-amber-200">Grafik interaktif tidak dapat dimuat. Data presensi tersedia dalam tabel berikut.</p>
                        <div class="mt-3 overflow-x-auto">
                            <table class="w-full min-w-[360px] text-left text-xs">
                                <thead><tr class="border-b border-slate-700 text-slate-400"><th class="py-2 pr-3">Hari</th><th class="py-2 pr-3 text-right text-cyan-300">Hadir</th><th class="py-2 text-right text-amber-300">Tidak Hadir</th></tr></thead>
                                <tbody>
                                    @foreach (($grafikLabel ?? []) as $index => $label)
                                        <tr class="border-b border-slate-800"><th scope="row" class="py-2 pr-3 font-medium">{{ $label }}</th><td class="py-2 pr-3 text-right tabular-nums">{{ $grafikHadir[$index] ?? 0 }}</td><td class="py-2 text-right tabular-nums">{{ $grafikTidakHadir[$index] ?? 0 }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <details class="mt-2 text-xs text-slate-500">
                    <summary class="w-fit cursor-pointer rounded-sm focus:outline-none focus:ring-2 focus:ring-cyan-300">Lihat data dalam tabel</summary>
                    <div class="mt-2 overflow-x-auto">
                        <table class="w-full min-w-[360px] text-left text-xs text-slate-300">
                            <thead><tr class="border-b border-slate-700 text-slate-400"><th class="py-2 pr-3">Hari</th><th class="py-2 pr-3 text-right text-cyan-300">Hadir</th><th class="py-2 text-right text-amber-300">Tidak Hadir</th></tr></thead>
                            <tbody>
                                @foreach (($grafikLabel ?? []) as $index => $label)
                                    <tr class="border-b border-slate-800"><th scope="row" class="py-2 pr-3 font-medium">{{ $label }}</th><td class="py-2 pr-3 text-right tabular-nums">{{ $grafikHadir[$index] ?? 0 }}</td><td class="py-2 text-right tabular-nums">{{ $grafikTidakHadir[$index] ?? 0 }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            </div>

        </div>


        {{-- QUICK ACTION --}}
        <div
            class="glass-panel rounded-xl sm:rounded-2xl
                   p-4 sm:p-5">

            <div class="flex items-start justify-between mb-4">

                <div>
                    <p
                        class="text-[10px] sm:text-xs
                               font-bold uppercase
                               tracking-[0.18em]
                               text-cyan-300">
                        Workspace
                    </p>

                    <h2
                        class="font-bold text-base sm:text-lg
                               text-white mt-2">
                        Aksi cepat
                    </h2>
                </div>

                <span class="text-xl text-cyan-200">
                    ↗
                </span>

            </div>


            <div
                class="grid grid-cols-1 sm:grid-cols-2
                       xl:grid-cols-1 gap-3">


                @if (auth()->user()->hasPermission('presensi.input'))

                    <a
                        href="{{ route('presensi.input') }}"
                        class="flex items-center justify-between gap-3
                               rounded-xl border border-slate-700
                               bg-slate-950/40
                               px-4 py-3
                               text-sm font-semibold text-slate-200
                               transition
                               hover:border-cyan-400/50
                               hover:bg-slate-950/60
                               hover:text-cyan-300">

                        <span>
                            Input presensi
                        </span>

                        <span class="text-cyan-300">
                            →
                        </span>

                    </a>

                @endif


                <a
                    href="{{ route('presensi.log') }}"
                    class="flex items-center justify-between gap-3
                           rounded-xl border border-slate-700
                           bg-slate-950/40
                           px-4 py-3
                           text-sm font-semibold text-slate-200
                           transition
                           hover:border-cyan-400/50
                           hover:bg-slate-950/60
                           hover:text-cyan-300">

                    <span>
                        Log & laporan
                    </span>

                    <span class="text-cyan-300">
                        →
                    </span>

                </a>


                <a
                    href="{{ route('karyawan.index') }}"
                    class="flex items-center justify-between gap-3
                           rounded-xl border border-slate-700
                           bg-slate-950/40
                           px-4 py-3
                           text-sm font-semibold text-slate-200
                           transition
                           hover:border-cyan-400/50
                           hover:bg-slate-950/60
                           hover:text-cyan-300">

                    <span>
                        Kelola karyawan
                    </span>

                    <span class="text-cyan-300">
                        →
                    </span>

                </a>

            </div>

        </div>

    </section>

    @if (in_array($user->role, ['admin_hr', 'super_admin'], true))
        <section class="mb-4 grid gap-4 lg:grid-cols-[1.1fr_.9fr] sm:mb-6">
            <div class="glass-panel rounded-xl p-4 sm:rounded-2xl sm:p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-cyan-300">Admin control center</p>
                        <h2 class="mt-2 text-lg font-extrabold text-white sm:text-xl">Kelola akun operasional</h2>
                        <p class="mt-1 text-sm text-slate-400">Buat akses mandor, reset password, atau cabut akses akun dari dashboard.</p>
                    </div>
                    <a href="{{ route('mandor.index') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-cyan-400/30 bg-cyan-400/10 px-3 py-2 text-xs font-bold text-cyan-200 hover:bg-cyan-400/20">Buka halaman lengkap <span aria-hidden="true">→</span></a>
                </div>

                <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-700 bg-slate-950/40 p-3"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Akun operasional</p><p class="mt-2 text-2xl font-extrabold text-white">{{ $mandorList->count() }}</p></div>
                    <div class="rounded-xl border border-slate-700 bg-slate-950/40 p-3"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Role tersedia</p><p class="mt-2 text-2xl font-extrabold text-cyan-300">{{ $assignableRoles->count() }}</p></div>
                    <div class="col-span-2 rounded-xl border border-emerald-400/20 bg-emerald-400/5 p-3 sm:col-span-1"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Akses Anda</p><p class="mt-2 text-sm font-extrabold text-emerald-300">Semua fitur aktif</p></div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[620px] text-left text-sm">
                        <thead><tr class="border-b border-slate-700 text-[10px] uppercase tracking-wide text-slate-500"><th class="pb-3 pr-3">Akun</th><th class="pb-3 pr-3">Role / Area</th><th class="pb-3 text-right">Tindakan</th></tr></thead>
                        <tbody>
                            @forelse ($mandorList as $mandor)
                                <tr class="border-b border-slate-800/80">
                                    <td class="py-3 pr-3"><p class="font-bold text-white">{{ $mandor->name }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $mandor->username }} · {{ $mandor->email }}</p></td>
                                    <td class="py-3 pr-3"><p class="text-xs font-semibold text-cyan-200">{{ ucwords(str_replace('_', ' ', $mandor->role)) }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $mandor->area ?: 'Area umum' }}</p></td>
                                    <td class="py-3 text-right"><div class="flex justify-end gap-2">@if ($user->role === 'super_admin')<button type="button" onclick="openPasswordInfo({{ $mandor->id }}, @js($mandor->name), @js($mandor->username))" class="inline-flex items-center justify-center rounded-lg border border-blue-300/40 bg-blue-50 px-2.5 py-1.5 text-blue-600 hover:bg-blue-100" aria-label="Lihat status password" title="Lihat status password"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.576 3.01 9.964 7.183a1.012 1.012 0 010 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.576-3.01-9.964-7.183z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></button>@endif<button type="button" onclick="openResetPassword({{ $mandor->id }}, @js($mandor->name))" class="rounded-lg border border-amber-300/30 bg-amber-300/10 px-2.5 py-1.5 text-xs font-bold text-amber-200 hover:bg-amber-300/20">Reset password</button><form method="POST" action="{{ route('mandor.destroy', $mandor) }}" onsubmit="return confirm('Hapus akun ini? Tindakan ini tidak dapat dibatalkan.')">@csrf @method('DELETE')<button type="submit" class="delete-action inline-flex items-center gap-1.5 rounded-lg border border-red-300/40 bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 hover:text-red-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-1.327L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-10.15 0a48.11 48.11 0 013.478-.397m7.172 0V4.5A2.25 2.25 0 0014 2.25h-4.5A2.25 2.25 0 007.25 4.5v.893m7.172 0a48.11 48.11 0 00-7.172 0" /></svg>Hapus</button></form></div></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-center text-sm text-slate-500">Belum ada akun operasional.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass-panel rounded-xl p-4 sm:rounded-2xl sm:p-6">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-cyan-300">Provisioning</p>
                <h2 class="mt-2 text-lg font-extrabold text-white">Buat akun mandor</h2>
                <p class="mb-5 mt-1 text-sm text-slate-400">Akun baru langsung bisa digunakan sesuai role dan area kerja.</p>
                <form method="POST" action="{{ route('mandor.store') }}" class="space-y-3">
                    @csrf
                    <input name="name" required placeholder="Nama lengkap" class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-cyan-400 focus:outline-none">
                    <input name="username" required placeholder="Username" class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-cyan-400 focus:outline-none">
                    <input name="email" type="email" required placeholder="Email kerja" class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-cyan-400 focus:outline-none">
                    <div class="relative"><input id="dashboardMandorPassword" name="password" type="password" minlength="8" required placeholder="Password awal" class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 pr-20 text-sm text-white placeholder:text-slate-600 focus:border-cyan-400 focus:outline-none"><button type="button" onclick="toggleAdminPassword('dashboardMandorPassword', this)" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md px-2 py-1 text-xs font-bold text-blue-600 hover:bg-blue-50">Lihat</button></div>
                    <select name="role_id" required class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"><option value="">Pilih role</option>@foreach ($assignableRoles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select>
                    <input name="area" placeholder="Area kerja (opsional)" class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-sm text-white placeholder:text-slate-600 focus:border-cyan-400 focus:outline-none">
                    <button type="submit" class="w-full rounded-lg bg-cyan-400 px-4 py-2.5 text-sm font-extrabold text-slate-950 transition hover:bg-cyan-300">Buat akun</button>
                </form>
            </div>
        </section>

        <div id="resetPasswordModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4">
            <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900 p-5 shadow-2xl">
                <div class="mb-5 flex items-start justify-between gap-4"><div><p class="text-[10px] font-bold uppercase tracking-[0.18em] text-amber-300">Security action</p><h2 class="mt-2 text-lg font-extrabold text-white">Reset password mandor</h2><p id="resetPasswordName" class="mt-1 text-sm text-slate-400"></p></div><button type="button" onclick="closeResetPassword()" class="text-xl text-slate-400 hover:text-white" aria-label="Tutup">×</button></div>
                <form id="resetPasswordForm" method="POST" class="space-y-3">@csrf @method('PUT')<div class="relative"><input id="resetMandorPassword" name="password" type="password" minlength="8" required placeholder="Password baru (minimal 8 karakter)" class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 pr-20 text-sm text-white placeholder:text-slate-600 focus:border-amber-300 focus:outline-none"><button type="button" onclick="toggleAdminPassword('resetMandorPassword', this)" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md px-2 py-1 text-xs font-bold text-blue-600 hover:bg-blue-50">Lihat</button></div><div class="relative"><input id="resetMandorPasswordConfirmation" name="password_confirmation" type="password" minlength="8" required placeholder="Ulangi password baru" class="w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 pr-20 text-sm text-white placeholder:text-slate-600 focus:border-amber-300 focus:outline-none"><button type="button" onclick="toggleAdminPassword('resetMandorPasswordConfirmation', this)" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md px-2 py-1 text-xs font-bold text-blue-600 hover:bg-blue-50">Lihat</button></div><button type="submit" class="w-full rounded-lg bg-amber-300 px-4 py-2.5 text-sm font-extrabold text-slate-950 hover:bg-amber-200">Simpan password baru</button></form>
            </div>
        </div>

        <div id="passwordInfoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4">
            <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900 p-5 shadow-2xl">
                <div class="mb-5 flex items-start justify-between gap-4"><div><p class="text-[10px] font-bold uppercase tracking-[0.18em] text-blue-300">Password account</p><h2 class="mt-2 text-lg font-extrabold text-white">Status password mandor</h2><p id="passwordInfoName" class="mt-1 text-sm text-slate-400"></p></div><button type="button" onclick="closePasswordInfo()" class="text-xl text-slate-400 hover:text-white" aria-label="Tutup">×</button></div>
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">Password lama tidak dapat ditampilkan karena disimpan sebagai hash untuk keamanan. Gunakan reset password untuk membuat password baru.</div>
                <button type="button" onclick="closePasswordInfo(); openResetPassword(window.passwordInfoUserId, window.passwordInfoUserName)" class="mt-4 w-full rounded-lg bg-amber-300 px-4 py-2.5 text-sm font-extrabold text-slate-950 hover:bg-amber-200">Reset password akun ini</button>
            </div>
        </div>
    @endif

</div>


@if (in_array($user->role, ['admin_hr', 'super_admin'], true))
    <script>
        function toggleAdminPassword(inputId, button) {
            const input = document.getElementById(inputId);
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.textContent = visible ? 'Lihat' : 'Sembunyikan';
        }

        function openResetPassword(userId, userName) {
            const modal = document.getElementById('resetPasswordModal');
            document.getElementById('resetPasswordName').textContent = `Akun: ${userName}`;
            document.getElementById('resetPasswordForm').action = `/mandor/${userId}/password`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function openPasswordInfo(userId, userName, username) {
            window.passwordInfoUserId = userId;
            window.passwordInfoUserName = userName;
            document.getElementById('passwordInfoName').textContent = `${userName} · ${username}`;
            const modal = document.getElementById('passwordInfoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closePasswordInfo() {
            const modal = document.getElementById('passwordInfoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        function closeResetPassword() {
            const modal = document.getElementById('resetPasswordModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endif

<script>
    const chartCanvas = document.getElementById('grafikKehadiran');
    const chartWrap = document.getElementById('grafikKehadiranWrap');
    const fallback = document.getElementById('grafikKehadiranFallback');

    if (chartCanvas && chartWrap && fallback) {
        const labels = @json($grafikLabel ?? []);
        const presentValues = @json($grafikHadir ?? []);
        const absentValues = @json($grafikTidakHadir ?? []);
        const axis = document.getElementById('grafikKehadiranAxis');
        const dayLabels = document.getElementById('grafikKehadiranDays');
        const tooltip = document.getElementById('grafikKehadiranTooltip');
        let resizeObserver;
        const context = chartCanvas.getContext('2d');
        const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
        let reducedMotion = motionPreference.matches;

        const showFallback = () => {
            fallback.classList.remove('hidden');
            chartCanvas.classList.add('hidden');
            axis.classList.add('hidden');
            dayLabels.classList.add('hidden');
        };

        const makeAxis = (maximum) => {
            axis.replaceChildren();
            [maximum, Math.round(maximum * 2 / 3), Math.round(maximum / 3), 0].forEach((value) => {
                const tick = document.createElement('span');
                tick.textContent = String(value);
                axis.append(tick);
            });
            dayLabels.replaceChildren();
            labels.forEach((label) => {
                const day = document.createElement('span');
                day.className = 'truncate px-px';
                day.textContent = String(label);
                dayLabels.append(day);
            });
        };

        if (!context) {
            showFallback();
        } else {
            const values = [...presentValues, ...absentValues].map((value) => Number(value) || 0);
            const maximum = Math.max(1, ...values);
            const scaleTop = Math.ceil(maximum / 5) * 5 || 1;
            const series = [
                { name: 'Hadir', values: presentValues, color: '#22d3ee', shade: '#0891b2' },
                { name: 'Tidak Hadir', values: absentValues, color: '#fbbf24', shade: '#d97706' }
            ];
            let plottedBars = [];
            let plotBounds = null;
            let pixelRatio = 1;
            let hoveredDay = null;
            let chartAnimationFrame = 0;
            let pageIsVisible = document.visibilityState === 'visible';

            makeAxis(scaleTop);

            const drawChart = (timestamp = performance.now()) => {
                const width = chartCanvas.clientWidth;
                const height = chartCanvas.clientHeight;
                if (!width || !height) return;

                context.clearRect(0, 0, width, height);
                plotBounds = { left: 8, right: width - 8, top: 10, bottom: height - 12 };
                const plotHeight = plotBounds.bottom - plotBounds.top;

                [1, 2 / 3, 1 / 3, 0].forEach((fraction) => {
                    const y = plotBounds.top + plotHeight * (1 - fraction);
                    context.beginPath();
                    context.moveTo(plotBounds.left, y);
                    context.lineTo(plotBounds.right, y);
                    context.strokeStyle = 'rgba(148, 163, 184, 0.16)';
                    context.lineWidth = 1;
                    context.stroke();
                });

                const groupWidth = (plotBounds.right - plotBounds.left) / Math.max(labels.length, 1);
                const barGap = Math.min(7, Math.max(3, groupWidth * 0.08));
                const barWidth = Math.max(3, Math.min(18, (groupWidth - barGap - 8) / 2));
                const groupBarsWidth = barWidth * 2 + barGap;
                plottedBars = [];

                const hoveredIndex = labels.findIndex((label) => String(label) === hoveredDay);
                if (hoveredIndex >= 0) {
                    context.fillStyle = 'rgba(34, 211, 238, 0.08)';
                    context.fillRect(plotBounds.left + hoveredIndex * groupWidth + 2, plotBounds.top, groupWidth - 4, plotHeight);
                }

                labels.forEach((label, index) => {
                    const groupLeft = plotBounds.left + index * groupWidth + (groupWidth - groupBarsWidth) / 2;

                    series.forEach((item, seriesIndex) => {
                        const count = Number(item.values[index]) || 0;
                        const finalHeight = (count / scaleTop) * plotHeight;
                        const barHeight = finalHeight;
                        const x = groupLeft + seriesIndex * (barWidth + barGap);
                        const y = plotBounds.bottom - barHeight;
                        const gradient = context.createLinearGradient(x, plotBounds.bottom - finalHeight, x, plotBounds.bottom);
                        gradient.addColorStop(0, item.color);
                        gradient.addColorStop(1, item.shade);

                        context.beginPath();
                        if (typeof context.roundRect === 'function') {
                            context.roundRect(x, y, barWidth, Math.max(barHeight, 1), [Math.min(5, barWidth / 2), Math.min(5, barWidth / 2), 1, 1]);
                        } else {
                            context.rect(x, y, barWidth, Math.max(barHeight, 1));
                        }
                        context.fillStyle = gradient;
                        context.shadowColor = `${item.color}55`;
                        context.shadowBlur = 5;
                        context.fill();
                        context.shadowBlur = 0;

                        if (barWidth > 7 && barHeight > 5) {
                            context.fillStyle = 'rgba(255, 255, 255, 0.28)';
                            context.fillRect(x + 2, y + 2, barWidth - 4, 1);
                        }

                        if (!reducedMotion && barHeight > 8) {
                            const flowProgress = (timestamp / 1900 + index * 0.17 + seriesIndex * 0.34) % 1;
                            const streamWidth = Math.max(1.5, Math.min(3, barWidth * 0.24));
                            const streamHeight = Math.min(30, Math.max(14, barHeight * 0.42));
                            const streamX = x + (barWidth - streamWidth) / 2;
                            const streamY = plotBounds.bottom - flowProgress * (barHeight + streamHeight);
                            context.save();
                            context.beginPath();
                            if (typeof context.roundRect === 'function') {
                                context.roundRect(x, y, barWidth, Math.max(barHeight, 1), [Math.min(5, barWidth / 2), Math.min(5, barWidth / 2), 1, 1]);
                            } else {
                                context.rect(x, y, barWidth, Math.max(barHeight, 1));
                            }
                            context.clip();
                            const streamGradient = context.createLinearGradient(0, streamY, 0, streamY + streamHeight);
                            streamGradient.addColorStop(0, 'rgba(255, 255, 255, 0)');
                            streamGradient.addColorStop(0.28, `${item.color}55`);
                            streamGradient.addColorStop(0.52, '#ffffff');
                            streamGradient.addColorStop(0.72, `${item.color}bb`);
                            streamGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
                            context.fillStyle = streamGradient;
                            context.shadowColor = item.color;
                            context.shadowBlur = 8;
                            context.fillRect(streamX, streamY, streamWidth, streamHeight);
                            context.shadowBlur = 0;
                            context.restore();
                        }

                        if (groupWidth >= 52) {
                            context.fillStyle = '#65594e';
                            context.font = '700 10px Manrope, sans-serif';
                            context.textAlign = 'center';
                            context.textBaseline = 'bottom';
                            context.fillText(String(count), x + barWidth / 2, Math.max(plotBounds.top + 10, y - 4));
                        }

                        plottedBars.push({
                            x,
                            y: plotBounds.bottom - finalHeight,
                            width: barWidth,
                            height: finalHeight,
                            day: String(label),
                            series: item.name,
                            count
                        });
                    });
                });
            };

            const resize = () => {
                const bounds = chartCanvas.getBoundingClientRect();
                if (!bounds.width || !bounds.height) return;
                pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
                chartCanvas.width = Math.round(bounds.width * pixelRatio);
                chartCanvas.height = Math.round(bounds.height * pixelRatio);
                context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
                drawChart();
            };

            const animateLightFlow = (timestamp) => {
                if (!pageIsVisible || reducedMotion) {
                    chartAnimationFrame = 0;
                    return;
                }
                drawChart(timestamp);
                chartAnimationFrame = requestAnimationFrame(animateLightFlow);
            };

            const startLightFlow = () => {
                if (!reducedMotion && pageIsVisible && !chartAnimationFrame) {
                    chartAnimationFrame = requestAnimationFrame(animateLightFlow);
                }
            };

            const findNearestBar = (event, threshold = 22) => {
                const bounds = chartCanvas.getBoundingClientRect();
                const x = event.clientX - bounds.left;
                const y = event.clientY - bounds.top;
                let nearest = null;
                let nearestDistance = threshold;
                plottedBars.forEach((bar) => {
                    const closestX = Math.max(bar.x, Math.min(x, bar.x + bar.width));
                    const closestY = Math.max(bar.y, Math.min(y, bar.y + Math.max(bar.height, 1)));
                    const distance = Math.hypot(closestX - x, closestY - y);
                    if (distance < nearestDistance) {
                        nearest = bar;
                        nearestDistance = distance;
                    }
                });
                return nearest;
            };

            const showPointTooltip = (event, point) => {
                if (!point) {
                    tooltip.classList.add('hidden');
                    chartCanvas.style.cursor = 'default';
                    return;
                }
                const wrapperBounds = chartWrap.getBoundingClientRect();
                tooltip.textContent = `${point.day} · ${point.series}: ${point.count}`;
                tooltip.style.left = `${Math.max(4, Math.min(event.clientX - wrapperBounds.left + 12, wrapperBounds.width - 180))}px`;
                tooltip.style.top = `${Math.max(4, event.clientY - wrapperBounds.top - 34)}px`;
                tooltip.classList.remove('hidden');
                chartCanvas.style.cursor = 'pointer';
            };

            const updateHoveredBar = (event, threshold) => {
                const bar = findNearestBar(event, threshold);
                const nextHoveredDay = bar?.day ?? null;
                if (hoveredDay !== nextHoveredDay) {
                    hoveredDay = nextHoveredDay;
                    drawChart();
                }
                showPointTooltip(event, bar);
            };

            resizeObserver = new ResizeObserver(resize);
            resizeObserver.observe(chartCanvas);
            resize();
            startLightFlow();

            chartCanvas.addEventListener('pointermove', (event) => {
                updateHoveredBar(event, 22);
            });
            chartCanvas.addEventListener('pointerup', (event) => {
                updateHoveredBar(event, 36);
            });
            chartCanvas.addEventListener('pointerleave', () => {
                hoveredDay = null;
                drawChart();
                tooltip.classList.add('hidden');
                chartCanvas.style.cursor = 'default';
            });

            motionPreference.addEventListener('change', (event) => {
                reducedMotion = event.matches;
                if (reducedMotion) {
                    if (chartAnimationFrame) cancelAnimationFrame(chartAnimationFrame);
                    chartAnimationFrame = 0;
                    drawChart();
                } else {
                    startLightFlow();
                }
            });

            document.addEventListener('visibilitychange', () => {
                pageIsVisible = document.visibilityState === 'visible';
                if (!pageIsVisible && chartAnimationFrame) {
                    cancelAnimationFrame(chartAnimationFrame);
                    chartAnimationFrame = 0;
                    drawChart();
                } else if (pageIsVisible) {
                    startLightFlow();
                }
            });

            window.addEventListener('pagehide', () => {
                if (chartAnimationFrame) cancelAnimationFrame(chartAnimationFrame);
                resizeObserver?.disconnect();
            }, { once: true });
        }
    }
</script>

@endsection