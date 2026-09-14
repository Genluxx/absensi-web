@extends('layouts.app')

@section('title', 'Dashboard - SAP.HRIS')
@section('page-title', 'Dashboard')

@section('content')
    <div class="relative overflow-hidden rounded-2xl border border-cyan-300/20 bg-[#123653] p-6 md:p-8 mb-6 shadow-xl shadow-slate-950/20">
        <div class="absolute right-0 top-0 h-full w-2/5 opacity-20" style="background-image: linear-gradient(135deg, transparent 25%, #69d2e7 25%, #69d2e7 26%, transparent 26%, transparent 50%, #69d2e7 50%, #69d2e7 51%, transparent 51%); background-size: 28px 28px;"></div>
        <div class="relative flex flex-col md:flex-row md:items-end md:justify-between gap-5">
            <div>
                <div class="flex items-center gap-2 mb-4"><span class="h-2 w-2 rounded-full bg-emerald-300 shadow-[0_0_12px_rgba(110,231,183,.7)]"></span><p class="text-xs font-bold uppercase tracking-[0.24em] text-cyan-200">Operational overview</p></div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white mb-2">Selamat datang, {{ $user->name }}</h1>
                <p class="text-slate-300 max-w-xl">Pantau kesiapan tim dan ambil tindakan dari satu ruang kerja. <span class="text-cyan-100">{{ now()->translatedFormat('l, d F Y') }}</span></p>
            </div>
            <div class="relative flex flex-col items-start md:items-end gap-3">
                <span class="text-xs font-semibold text-cyan-100/60">Status sistem</span>
                <a href="{{ route('presensi.log') }}" class="glow-button inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-[#123451] hover:bg-cyan-50">
                    Buka laporan
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L19 10m0 0l-5.5 5.5M19 10H5" /></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
        <div class="glass-panel rounded-2xl p-5 xl:col-span-1 border-t-2 border-cyan-300">
            <div class="flex items-center justify-between"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Total tim</p><span class="text-cyan-300">◈</span></div>
            <p class="text-3xl font-extrabold text-white mt-4">{{ $totalKaryawan ?? '-' }}</p>
            <p class="text-xs text-slate-500 mt-2">Anggota terdaftar</p>
        </div>
        <div class="glass-panel rounded-2xl p-5 xl:col-span-1 border-t-2 border-emerald-300">
            <div class="flex items-center justify-between"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Hadir</p><span class="text-emerald-300">●</span></div>
            <p class="text-3xl font-extrabold text-emerald-300 mt-4">{{ $hadirHariIni ?? '-' }}</p>
            <p class="text-xs text-slate-500 mt-2">Termasuk status telat</p>
        </div>
        <div class="glass-panel rounded-2xl p-5 xl:col-span-1 border-t-2 border-amber-300">
            <div class="flex items-center justify-between"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Izin / sakit</p><span class="text-amber-300">●</span></div>
            <p class="text-3xl font-extrabold text-amber-300 mt-4">{{ $izinSakit ?? '-' }}</p>
            <p class="text-xs text-slate-500 mt-2">Perlu tindak lanjut</p>
        </div>
        <div class="glass-panel rounded-2xl p-5 xl:col-span-1 border-t-2 border-rose-300">
            <div class="flex items-center justify-between"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Alpa</p><span class="text-rose-300">●</span></div>
            <p class="text-3xl font-extrabold text-rose-300 mt-4">{{ $alpa ?? '-' }}</p>
            <p class="text-xs text-slate-500 mt-2">Perlu perhatian segera</p>
        </div>
        <div class="glass-panel rounded-2xl p-5 xl:col-span-1 border-t-2 border-blue-300">
            <div class="flex items-center justify-between"><p class="text-xs font-bold uppercase tracking-wide text-slate-400">Kehadiran</p><span class="text-blue-300">↗</span></div>
            <p class="text-3xl font-extrabold text-blue-200 mt-4">{{ $persentaseHadir }}%</p>
            <p class="text-xs text-slate-500 mt-2">{{ $belumAbsen }} belum tercatat</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_280px] gap-6">
        <div class="glass-panel rounded-2xl p-5 md:p-6">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div><h2 class="font-bold text-lg text-white">Grafik Kehadiran Tim Operasional</h2><p class="text-sm text-slate-400 mt-1">Statistik presensi 7 hari terakhir</p></div>
                <span class="hidden sm:inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs text-emerald-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>Data terbaru</span>
            </div>
            <div class="h-72"><canvas id="grafikKehadiran"></canvas></div>
        </div>

        <div class="glass-panel rounded-2xl p-5">
            <div class="flex items-start justify-between mb-4"><div><p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-300">Workspace</p><h2 class="font-bold text-lg text-white mt-2">Aksi cepat</h2></div><span class="text-xl text-cyan-200">↗</span></div>
            <div class="space-y-3">
                @if (auth()->user()->hasPermission('presensi.input'))
                <a href="{{ route('presensi.input') }}" class="flex items-center justify-between rounded-xl border border-slate-700 bg-slate-950/40 px-4 py-3 text-sm font-semibold text-slate-200 hover:border-cyan-400/50 hover:text-cyan-300 transition"><span>Input presensi</span><span class="text-cyan-300">→</span></a>
                @endif
                <a href="{{ route('presensi.log') }}" class="flex items-center justify-between rounded-xl border border-slate-700 bg-slate-950/40 px-4 py-3 text-sm font-semibold text-slate-200 hover:border-cyan-400/50 hover:text-cyan-300 transition"><span>Log & laporan</span><span class="text-cyan-300">→</span></a>
                <a href="{{ route('karyawan.index') }}" class="flex items-center justify-between rounded-xl border border-slate-700 bg-slate-950/40 px-4 py-3 text-sm font-semibold text-slate-200 hover:border-cyan-400/50 hover:text-cyan-300 transition"><span>Kelola karyawan</span><span class="text-cyan-300">→</span></a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('grafikKehadiran');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($grafikLabel) !!},
                datasets: [
                    {
                        label: 'Hadir',
                        data: {!! json_encode($grafikHadir) !!},
                        backgroundColor: '#34D399',
                        borderRadius: 4,
                    },
                    {
                        label: 'Tidak Hadir',
                        data: {!! json_encode($grafikTidakHadir) !!},
                        backgroundColor: '#F87171',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#cbd5e1', usePointStyle: true, padding: 20 } },
                    tooltip: { backgroundColor: '#0f172a', borderColor: 'rgba(34, 211, 238, 0.35)', borderWidth: 1, padding: 12 }
                },
                scales: {
                    x: { ticks: { color: '#cbd5e1' }, grid: { color: 'rgba(148,163,184,0.15)' } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, color: '#cbd5e1' }, grid: { color: 'rgba(148,163,184,0.15)' } }
                }
            }
        });
    </script>
@endsection