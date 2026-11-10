@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="sparkles" class="w-6 h-6 text-amber-500"></i>
                Kandidat Kecocokan AI
            </h1>
            <p class="text-xs text-slate-500 mt-1">Barang ditemukan yang cocok dengan laporan kehilangan Anda berdasarkan analisis kecerdasan buatan Google Gemini.</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 text-indigo-700 text-xs font-bold self-start">
            <i data-lucide="cpu" class="w-4 h-4 text-indigo-600"></i>
            Powered by Google Gemini AI
        </span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang Hilang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang Ditemukan</th>
                        <th class="text-center px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Skor</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Confidence</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-center px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($matches as $match)
                        <tr class="hover:bg-amber-50/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-slate-900 text-sm block">{{ $match->lostItem?->title }}</span>
                                <span class="text-xs text-slate-400">{{ $match->lostItem?->item_code }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $match->foundItem?->photos[0] ?? '' }}" alt="" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                                    <div>
                                        <span class="font-semibold text-slate-900 text-sm block">{{ $match->foundItem?->title }}</span>
                                        <span class="text-xs text-slate-400">{{ $match->foundItem?->found_location }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="text-xl font-extrabold {{ $match->match_score >= 80 ? 'text-emerald-600' : ($match->match_score >= 60 ? 'text-amber-600' : 'text-slate-400') }}">
                                    {{ $match->match_score }}%
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $confColors = ['SANGAT_TINGGI' => 'bg-emerald-100 text-emerald-700', 'TINGGI' => 'bg-blue-100 text-blue-700', 'SEDANG' => 'bg-amber-100 text-amber-700', 'RENDAH' => 'bg-slate-100 text-slate-600'];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $confColors[$match->confidence_level] ?? 'bg-slate-100 text-slate-500' }}">
                                    {{ str_replace('_', ' ', $match->confidence_level) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <x-status-badge :status="$match->status" />
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('matches.show', $match->id) }}" class="text-xs font-bold text-blue-600 hover:underline">Detail &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i data-lucide="sparkles" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Belum ada kandidat kecocokan AI.</p>
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
