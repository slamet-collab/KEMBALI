@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="file-text" class="w-6 h-6 text-slate-600"></i>
                Log Aktivitas & Audit Trail Sistem
            </h1>
            <p class="text-xs text-slate-500 mt-1">Catatan rekaman aksi pengguna, perubahan status barang, verifikasi admin, dan serah terima.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Waktu</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Pengguna</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aktivitas / Aksi</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Keterangan</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y H:i:s') }}
                            </td>
                            <td class="px-5 py-3.5 text-xs">
                                <span class="font-bold text-slate-900 block">{{ $log->user?->name ?? 'Sistem' }}</span>
                                <span class="text-slate-400 font-mono">{{ $log->user?->email ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600 max-w-md">
                                {{ $log->description ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-400 font-mono">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">
                                <i data-lucide="clipboard-list" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                Belum ada catatan aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
