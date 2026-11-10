@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header & Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('claims.index') }}" 
               class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    Detail Klaim: {{ $claim->claim_code }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Diajukan pada {{ $claim->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
        </div>
        <x-status-badge :status="$claim->status" />
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Claim & Item Info -->
        <div class="md:col-span-2 space-y-6">
            <!-- Informasi Barang yang Diklaim -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i data-lucide="package" class="w-5 h-5 text-blue-600"></i>
                    Barang yang Diklaim
                </h2>
                
                @if($claim->foundItem)
                    <div class="flex items-start gap-4">
                        @if($claim->foundItem->primary_photo_url)
                            <img src="{{ asset('storage/' . $claim->foundItem->primary_photo_url) }}" 
                                 alt="{{ $claim->foundItem->title }}" 
                                 class="w-24 h-24 rounded-xl object-cover border border-slate-100 shrink-0">
                        @else
                            <div class="w-24 h-24 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                <i data-lucide="image" class="w-8 h-8"></i>
                            </div>
                        @endif
                        <div class="space-y-1">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700">
                                {{ $claim->foundItem->category?->name ?? 'Kategori Umum' }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-lg leading-snug">{{ $claim->foundItem->title }}</h3>
                            <p class="text-xs text-slate-500">Kode Barang: <span class="font-mono font-medium text-slate-700">{{ $claim->foundItem->item_code }}</span></p>
                            <p class="text-xs text-slate-500">Ditemukan di: <span class="font-medium text-slate-700">{{ $claim->foundItem->location_found ?? '-' }}</span></p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-500">Data barang temuan tidak tersedia.</p>
                @endif
            </div>

            <!-- Bukti Kepemilikan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i data-lucide="file-check" class="w-5 h-5 text-emerald-600"></i>
                    Bukti & Ciri Khusus
                </h2>

                <div class="space-y-3">
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase">Ciri-ciri Khusus yang Disebutkan:</span>
                        <p class="text-sm text-slate-800 mt-1 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            {{ $claim->special_characteristics }}
                        </p>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase">Bukti Kepemilikan:</span>
                        <p class="text-sm text-slate-800 mt-1 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            {{ $claim->ownership_proof }}
                        </p>
                    </div>

                    @if($claim->rejection_reason)
                        <div>
                            <span class="block text-xs font-semibold text-rose-500 uppercase">Alasan Penolakan:</span>
                            <p class="text-sm text-rose-700 mt-1 bg-rose-50 p-3 rounded-xl border border-rose-100">
                                {{ $claim->rejection_reason }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Status Tracker & Pengambilan -->
        <div class="space-y-6">
            <!-- Timeline Status -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i data-lucide="clock" class="w-5 h-5 text-indigo-600"></i>
                    Status Pengajuan
                </h2>

                <ul class="space-y-4 text-xs">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Klaim Diajukan</p>
                            <p class="text-slate-500">{{ $claim->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full {{ in_array($claim->status, ['DIPERIKSA', 'DISETUJUI', 'SIAP_DIAMBIL', 'SELESAI']) ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Verifikasi Petugas</p>
                            <p class="text-slate-500">
                                {{ $claim->verified_at ? $claim->verified_at->format('d M Y H:i') : 'Menunggu review' }}
                            </p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full {{ in_array($claim->status, ['SIAP_DIAMBIL', 'SELESAI']) ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Pengambilan Barang</p>
                            <p class="text-slate-500">
                                @if($claim->status === 'SIAP_DIAMBIL')
                                    <span class="text-amber-600 font-semibold">Siap diambil di Pos/Ruang Staf</span>
                                @elseif($claim->status === 'SELESAI')
                                    <span class="text-emerald-600 font-semibold">Barang telah diserahterimakan</span>
                                @else
                                    Belum siap diambil
                                @endif
                            </p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Identitas Pemohon -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-3">
                <h3 class="text-xs font-bold text-slate-500 uppercase">Pemohon Klaim</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center text-sm">
                        {{ strtoupper(substr($claim->user?->name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $claim->user?->name }}</p>
                        <p class="text-xs text-slate-500">{{ $claim->user?->identity_no ?? '-' }} ({{ $claim->user?->phone ?? 'No Phone' }})</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
