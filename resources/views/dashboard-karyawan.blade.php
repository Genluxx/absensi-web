@extends('layouts.app')

@section('title', 'Dashboard Karyawan - SAP.HRIS')
@section('page-title', 'Absensi Saya')

@section('content')
<div class="mx-auto w-full max-w-4xl">
    <section class="mb-6 rounded-2xl border border-cyan-300/20 bg-[#123653] p-6 shadow-xl shadow-slate-950/20">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-200">Absensi pribadi</p>
        <h1 class="mt-3 text-3xl font-extrabold text-white">Selamat datang, {{ $user->name }}</h1>
        <p class="mt-2 text-sm text-slate-300">{{ $karyawan->jabatan ?: 'Karyawan' }} · {{ $karyawan->lokasi }}</p>
        <p class="mt-1 text-sm text-cyan-100">{{ now()->translatedFormat('l, d F Y') }}</p>
    </section>

    <section class="glass-panel rounded-2xl p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-300">Status hari ini</p>
                <h2 class="mt-2 text-xl font-bold {{ $statusHariIni === 'Hadir' ? 'text-emerald-300' : ($statusHariIni === 'Cuti' ? 'text-orange-300' : (in_array($statusHariIni, ['Izin', 'Sakit']) ? 'text-yellow-300' : ($statusHariIni === 'Alpa' || $statusHariIni === 'Telat' ? 'text-red-300' : 'text-white'))) }}">
                    {{ $statusHariIni ?: 'Belum absen' }}
                </h2>
                @if ($presensiHariIni?->jam_masuk)
                    <p class="mt-1 text-sm text-slate-400">Dicatat pukul {{ $presensiHariIni->jam_masuk }}</p>
                @endif
                @if ($presensiHariIni?->keterangan)
                    <p class="mt-1 text-sm text-amber-300">{{ $presensiHariIni->keterangan }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-lg font-extrabold tracking-wide text-cyan-300">Jam <span id="employeeClock">--:--:--</span></p>
                <p class="mt-2 text-sm font-bold text-slate-300">Cuti: <span class="text-orange-300">{{ $cutiTerpakai }}/12</span> hari · Sisa <span class="text-emerald-300">{{ $cutiTersisa }}</span></p>
            </div>
        </div>

        <form method="POST" action="{{ route('presensi.simpan') }}" class="mt-6">
            @csrf
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach (['Hadir', 'Cuti', 'Izin', 'Sakit'] as $status)
                    <button type="submit" name="presensi[{{ $karyawan->id }}][status]" value="{{ $status }}"
                            class="rounded-xl border px-4 py-4 text-sm font-extrabold transition hover:brightness-110 {{ $status === 'Hadir' ? 'border-emerald-400/50 text-emerald-300' : ($status === 'Cuti' ? 'border-orange-400/50 text-orange-300' : 'border-yellow-400/50 text-yellow-300') }} {{ $presensiHariIni && ($presensiHariIni->status === $status || ($status === 'Hadir' && $presensiHariIni->status === 'Telat')) ? 'bg-white/10 ring-2 ring-white/40' : '' }}">
                        {{ $status }}
                    </button>
                @endforeach
            </div>
            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-bold text-slate-300">Mulai cuti</label>
                    <input type="date" name="cuti_mulai" min="{{ now()->format('Y-m-d') }}" class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300">Selesai cuti</label>
                    <input type="date" name="cuti_selesai" min="{{ now()->format('Y-m-d') }}" class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-white">
                </div>
            </div>
            <label class="mt-5 block text-xs font-bold text-slate-300">Keterangan (opsional)</label>
            <input type="text" name="presensi[{{ $karyawan->id }}][keterangan]"
                   value="{{ $presensiHariIni?->keterangan }}"
                   class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 text-white px-3 py-2"
                   placeholder="Alasan cuti, izin, atau sakit...">
            <p class="mt-3 text-xs text-slate-500">Hadir tepat waktu berwarna hijau, cuti oranye, izin/sakit kuning, dan alpa/telat merah. Hadir setelah pukul 08:00 otomatis menjadi Telat.</p>
        </form>
    </section>
</div>

<script>
    const employeeClock = document.getElementById('employeeClock');
    const updateEmployeeClock = () => {
        employeeClock.textContent = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
        }).format(new Date());
    };
    updateEmployeeClock();
    setInterval(updateEmployeeClock, 1000);
</script>
@endsection
