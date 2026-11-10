@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <a href="{{ route('matches.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>

    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-3xl border border-amber-200/80 p-6 space-y-3">
        <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
            <i data-lucide="sparkles" class="w-6 h-6 text-amber-500"></i>
            Detail Kecocokan AI — Skor {{ $match->match_score }}%
        </h1>
        @php
            $confColors = ['SANGAT_TINGGI' => 'bg-emerald-600', 'TINGGI' => 'bg-blue-600', 'SEDANG' => 'bg-amber-500', 'RENDAH' => 'bg-slate-400'];
        @endphp
        <span class="px-3 py-1 rounded-full text-white text-xs font-bold {{ $confColors[$match->confidence_level] ?? 'bg-slate-400' }}">
            {{ str_replace('_', ' ', $match->confidence_level) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Lost Item -->
        <div class="bg-white rounded-3xl border border-rose-200/80 shadow-sm p-6 space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span> Barang Hilang
            </h3>
            <div class="flex items-center gap-4">
                <img src="{{ $match->lostItem?->photos[0] ?? '' }}" alt="" class="w-16 h-16 rounded-xl object-cover border">
                <div>
                    <span class="font-bold text-slate-900 text-sm block">{{ $match->lostItem?->title }}</span>
                    <span class="text-xs text-slate-500">{{ $match->lostItem?->item_code }} &bull; {{ $match->lostItem?->category?->name }}</span>
                    <span class="text-xs text-slate-400 block">{{ $match->lostItem?->last_seen_location }} &bull; {{ $match->lostItem?->last_seen_date }}</span>
                    <span class="text-xs text-slate-400 block">Pelapor: {{ $match->lostItem?->user?->name }}</span>
                </div>
            </div>
        </div>

        <!-- Found Item -->
        <div class="bg-white rounded-3xl border border-emerald-200/80 shadow-sm p-6 space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Barang Ditemukan
            </h3>
            <div class="flex items-center gap-4">
                <img src="{{ $match->foundItem?->photos[0] ?? '' }}" alt="" class="w-16 h-16 rounded-xl object-cover border">
                <div>
                    <span class="font-bold text-slate-900 text-sm block">{{ $match->foundItem?->title }}</span>
                    <span class="text-xs text-slate-500">{{ $match->foundItem?->item_code }} &bull; {{ $match->foundItem?->category?->name }}</span>
                    <span class="text-xs text-slate-400 block">{{ $match->foundItem?->found_location }} &bull; {{ $match->foundItem?->found_date }}</span>
                    <span class="text-xs text-slate-400 block">Penemu: {{ $match->foundItem?->user?->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Match Reasons -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                <i data-lucide="brain" class="w-5 h-5 text-indigo-500"></i>
                Alasan & Analisis Kecocokan AI (Google Gemini)
            </h3>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-indigo-500"></i>
                Gemini AI Engine
            </span>
        </div>
        <ul class="space-y-2">
            @foreach($match->match_reasons ?? [] as $reason)
                <li class="flex items-start gap-2.5 text-sm text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                    <span>{{ $reason }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Action -->
    @if($match->status === 'PENDING')
        <div class="flex gap-3">
            <a href="{{ route('claims.create', $match->foundItem?->id) }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center gap-2">
                <i data-lucide="handshake" class="w-4 h-4"></i>
                Ajukan Klaim Barang Ini
            </a>
        </div>
    @endif
</div>
@endsection
