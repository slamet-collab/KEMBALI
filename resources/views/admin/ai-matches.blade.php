@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="sparkles" class="w-6 h-6 text-purple-600"></i>
                Kandidat AI Matching (Kecocokan Barang)
            </h1>
            <p class="text-xs text-slate-500 mt-1">Hasil pencocokan cerdas antara laporan barang hilang dan temuan berdasarkan kemiripan judul, deskripsi, lokasi, dan kategori.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang Hilang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang Ditemukan</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Skor Kecocokan</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Alasan Cocok</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status Konfirmasi</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($matches as $match)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900 block">{{ $match->lostItem?->title ?? 'Barang Hilang' }}</span>
                                <span class="text-xs text-blue-600 font-mono">{{ $match->lostItem?->item_code }}</span>
                                <span class="text-xs text-slate-400 block">Oleh: {{ $match->lostItem?->user?->name ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900 block">{{ $match->foundItem?->title ?? 'Barang Ditemukan' }}</span>
                                <span class="text-xs text-emerald-600 font-mono">{{ $match->foundItem?->item_code }}</span>
                                <span class="text-xs text-slate-400 block">Oleh: {{ $match->foundItem?->user?->name ?? 'Anonim' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full {{ $match->confidence_score >= 80 ? 'bg-emerald-500' : ($match->confidence_score >= 60 ? 'bg-amber-500' : 'bg-blue-500') }}" 
                                             style="width: {{ min(100, $match->confidence_score) }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700">{{ number_format($match->confidence_score, 0) }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600 max-w-xs">
                                <p class="truncate">{{ $match->match_reason ?? 'Kemiripan judul, kategori, dan rentang lokasi.' }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-lg {{ $match->is_confirmed ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $match->is_confirmed ? 'Terkonfirmasi' : 'Belum Konfirmasi' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('matches.show', $match->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-purple-600 hover:text-purple-700 bg-purple-50 hover:bg-purple-100 px-3 py-1.5 rounded-lg transition-colors">
                                    Lihat <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i data-lucide="sparkles" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Belum ada kecocokan barang yang terdeteksi AI.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($matches->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $matches->links() }}</div>
        @endif
    </div>
</div>
@endsection
