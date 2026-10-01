@extends('layouts.app')

@section('title', 'Audit Aktivitas - SAP.HRIS')
@section('page-title', 'Audit Aktivitas')

@section('content')
    <div class="glass-panel rounded-2xl p-4 sm:p-6">
        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-300">Security & governance</p>
                <h2 class="mt-2 text-xl font-extrabold text-white">Riwayat aktivitas admin</h2>
                <p class="mt-1 text-sm text-slate-400">Pantau perubahan penting pada presensi, koreksi, dan akun operasional.</p>
            </div>
            <form method="GET" class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                <select name="action" class="rounded-lg border px-3 py-2 text-sm">
                    <option value="">Semua aktivitas</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $action)) }}</option>
                    @endforeach
                </select>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" aria-label="Tanggal mulai" class="rounded-lg border px-3 py-2 text-sm">
                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" aria-label="Tanggal selesai" class="rounded-lg border px-3 py-2 text-sm">
                <button class="rounded-lg bg-gray-900 px-3 py-2 text-sm font-bold text-white sm:col-span-3">Terapkan filter</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full min-w-[780px] text-sm">
                <thead>
                    <tr class="border-b text-left text-xs text-gray-500">
                        <th class="px-3 py-3">Waktu</th>
                        <th class="px-3 py-3">Admin</th>
                        <th class="px-3 py-3">Aktivitas</th>
                        <th class="px-3 py-3">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="border-b">
                            <td class="px-3 py-3 whitespace-nowrap text-xs">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-3 py-3 font-bold">{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td class="px-3 py-3"><span class="rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">{{ ucwords(str_replace('_', ' ', $log->action)) }}</span></td>
                            <td class="px-3 py-3 text-slate-600">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-3 py-8 text-center text-slate-500">Belum ada aktivitas tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
@endsection
