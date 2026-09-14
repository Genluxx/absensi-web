@extends('layouts.app')

@section('title', 'Permintaan Koreksi - SAP.HRIS')
@section('page-title', 'Permintaan Koreksi')

@section('content')
    @if (!$isAdmin)
    <div class="glass-panel rounded-2xl p-4 sm:p-6 mb-6">
        <div class="mb-5">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-300 mb-2">Ajukan perubahan</p>
            <h2 class="font-bold text-lg text-white">Koreksi data presensi</h2>
            <p class="text-sm text-slate-400 mt-1">Pilih catatan presensi yang perlu diperbaiki. Pengajuan akan ditinjau admin.</p>
        </div>
        <form method="POST" action="{{ route('koreksi.store') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @csrf
            <div class="lg:col-span-2">
                <label for="presensi_id" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Catatan presensi</label>
                <select id="presensi_id" name="presensi_id" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2.5 text-sm" required>
                    <option value="">Pilih karyawan dan tanggal...</option>
                    @foreach ($presensiList as $presensi)
                        <option value="{{ $presensi->id }}" {{ old('presensi_id') == $presensi->id ? 'selected' : '' }}>
                            {{ $presensi->tanggal->format('d/m/Y') }} - {{ $presensi->karyawan->nama ?? 'Karyawan' }} - status {{ $presensi->status }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status_baru" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Status yang benar</label>
                <select id="status_baru" name="status_baru" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2.5 text-sm">
                    <option value="">Tidak diubah</option>
                    @foreach (['Hadir', 'Telat', 'Izin', 'Sakit', 'Alpa'] as $status)
                        <option value="{{ $status }}" {{ old('status_baru') === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="keterangan_baru" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Keterangan baru</label>
                <input id="keterangan_baru" type="text" name="keterangan_baru" value="{{ old('keterangan_baru') }}" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2.5 text-sm" placeholder="Opsional">
            </div>
            <div class="lg:col-span-2">
                <label for="alasan" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Alasan pengajuan</label>
                <textarea id="alasan" name="alasan" rows="3" required class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2.5 text-sm" placeholder="Jelaskan mengapa data perlu diperbaiki...">{{ old('alasan') }}</textarea>
            </div>
            <div class="lg:col-span-2 flex justify-end">
                <button type="submit" class="glow-button inline-flex items-center gap-2 rounded-xl bg-cyan-400 px-4 py-2.5 text-sm font-bold text-slate-950 hover:bg-cyan-300">Kirim permintaan</button>
            </div>
        </form>
    </div>
    @endif

    <div class="glass-panel rounded-2xl p-4 sm:p-6">
        <div class="mb-6">
            <h2 class="font-bold text-lg text-white">Daftar Permintaan Koreksi Presensi</h2>
            <p class="text-sm text-slate-400">
                {{ $isAdmin ? 'Semua permintaan dari seluruh mandor' : 'Permintaan koreksi yang Anda ajukan' }}
            </p>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-800">
        <table class="w-full min-w-[980px] text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                    <th class="pb-2">Tanggal Presensi</th>
                    <th class="pb-2">Karyawan</th>
                    <th class="pb-2">Status Lama</th>
                    <th class="pb-2">Diminta Jadi</th>
                    <th class="pb-2">Alasan</th>
                    <th class="pb-2">Mandor</th>
                    @if (!in_array($user->role, ['admin_hr', 'super_admin']))
                        <th class="pb-2">Aksi</th>
                    @endif
                    <th class="pb-2">Status</th>
                    @if ($isAdmin)
                        <th class="pb-2">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarKoreksi as $k)
                <tr class="border-b border-slate-800">
                    <td class="py-3 text-slate-300">{{ $k->presensi->tanggal->format('d/m/Y') }}</td>
                    <td class="py-3 font-bold text-white">{{ $k->presensi->karyawan->nama ?? '-' }}</td>
                    <td class="py-3 text-slate-300">{{ $k->presensi->status }}</td>
                    <td class="py-3 text-slate-300">{{ $k->status_baru ?? '-' }}</td>
                    <td class="py-3 text-xs max-w-xs text-slate-300">{{ $k->alasan }}</td>
                    <td class="py-3 text-slate-300">{{ $k->mandor->name }}</td>
                    <td class="py-3">
                        <span class="px-2 py-1 rounded text-xs font-bold
                            @if($k->status == 'pending') bg-amber-500/15 text-amber-300 border border-amber-500/30
                            @elseif($k->status == 'approved') bg-emerald-500/15 text-emerald-300 border border-emerald-500/30
                            @else bg-red-500/15 text-red-300 border border-red-500/30 @endif">
                            {{ ucfirst($k->status) }}
                        </span>
                        @if ($k->status === 'rejected' && $k->catatan_admin)
                            <p class="text-xs text-slate-400 mt-1">{{ $k->catatan_admin }}</p>
                        @endif
                    </td>
                    @if ($isAdmin)
                        <td class="py-3">
                            @if ($k->status === 'pending')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('koreksi.approve', $k->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-emerald-300 text-xs font-bold">Setujui</button>
                                    </form>
                                    <button onclick="document.getElementById('modalReject{{ $k->id }}').classList.remove('hidden')"
                                            class="text-red-300 text-xs font-bold">Tolak</button>
                                </div>

                                <div id="modalReject{{ $k->id }}" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center p-6 z-50">
                                    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full">
                                        <h3 class="font-bold text-lg mb-4 text-white">Tolak Permintaan Koreksi</h3>
                                        <form method="POST" action="{{ route('koreksi.reject', $k->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <label class="block text-xs font-bold mb-1 text-slate-300">Alasan Penolakan</label>
                                            <textarea name="catatan_admin" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4" rows="3" required></textarea>
                                            <div class="flex gap-2">
                                                <button type="button" onclick="document.getElementById('modalReject{{ $k->id }}').classList.add('hidden')"
                                                        class="flex-1 border border-slate-600 rounded-xl py-2 font-bold text-slate-200">Batal</button>
                                                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-500 text-white rounded-xl py-2 font-bold">Tolak</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-6 text-center text-slate-400">Belum ada permintaan koreksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="mt-4 text-slate-300">
            {{ $daftarKoreksi->links() }}
        </div>
    </div>
@endsection