@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="history" class="w-6 h-6 text-indigo-600"></i>
                Riwayat Serah Terima Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar arsip bukti penyerahan barang temuan kepada pemilik yang sah.</p>
        </div>
        <a href="{{ route('staff.handover') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
            <i data-lucide="handshake" class="w-4 h-4"></i>
            Serah Terima Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">No. Berita Acara</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Penerima</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Petugas Penyerah</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Tanggal & Jam</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                                    {{ $rec->handover_code }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900 block">{{ $rec->foundItem?->title ?? 'Barang' }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $rec->foundItem?->item_code }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-800 block text-xs">{{ $rec->claimedBy?->name ?? 'Pemilik' }}</span>
                                <span class="text-xs text-slate-500">ID: {{ $rec->recipient_identity_no }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-700 font-medium">
                                {{ $rec->handedOverBy?->name ?? 'Petugas' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $rec->handover_date ? $rec->handover_date->format('d M Y H:i') : $rec->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600 max-w-xs truncate">
                                {{ $rec->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i data-lucide="archive" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Belum ada riwayat serah terima barang.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $records->links() }}</div>
        @endif
    </div>
</div>
@endsection
