@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <!-- Welcome Banner (Matching PDF Page 2) -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 p-8 text-white shadow-xl shadow-blue-900/20">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-[url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&auto=format&fit=crop')] bg-cover bg-center opacity-10"></div>
        
        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-blue-200 text-xs font-bold tracking-wider uppercase border border-white/20">
                <i data-lucide="building" class="w-3.5 h-3.5 text-blue-300"></i>
                Universitas Pancasakti Tegal
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                Selamat datang, {{ explode(' ', $user->name)[0] }}! 👋
            </h1>

            <p class="text-blue-100 text-sm sm:text-base leading-relaxed">
                Yuk, bantu temukan barang yang hilang atau kembalikan barang yang ditemukan di lingkungan kampus.
            </p>

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="{{ route('lost-items.create') }}" class="px-5 py-2.5 rounded-xl bg-white text-blue-800 font-extrabold text-sm hover:bg-blue-50 shadow-md transition-all flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4 text-blue-600"></i>
                    Laporkan Barang Hilang
                </a>
                <a href="{{ route('found-items.create') }}" class="px-5 py-2.5 rounded-xl bg-blue-600/40 hover:bg-blue-600/60 text-white font-bold text-sm border border-white/30 backdrop-blur-md transition-all flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Laporkan Barang Ditemukan
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Cards Grid (4 Cards - Matching PDF Page 2) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Stat 1: Laporan Barang Hilang -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $myLostCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Barang Hilang</span>
                    <span class="text-[11px] font-semibold text-slate-400">Laporan Anda</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                    <i data-lucide="help-circle" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Stat 2: Laporan Barang Ditemukan -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $myFoundCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Barang Ditemukan</span>
                    <span class="text-[11px] font-semibold text-slate-400">Laporan Anda</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Stat 3: Kandidat Kecocokan -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $matchesCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Kandidat Kecocokan</span>
                    <span class="text-[11px] font-semibold text-amber-600">Menunggu konfirmasi</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                    <i data-lucide="sparkles" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Stat 4: Klaim Aktif -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $activeClaimsCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Klaim Aktif</span>
                    <span class="text-[11px] font-semibold text-blue-600">Sedang diverifikasi</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                    <i data-lucide="handshake" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Grid Section: Kandidat Kecocokan & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Kandidat Kecocokan Terbaru Card -->
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-5 h-5 text-amber-500"></i>
                    <h3 class="font-extrabold text-slate-900 text-lg">Kandidat Kecocokan Terbaru</h3>
                </div>
                <a href="{{ route('matches.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($topMatch)
                <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50/70 to-indigo-50/70 border border-blue-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <img src="{{ $topMatch->foundItem->photos[0] ?? 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=500&auto=format&fit=crop' }}" 
                             alt="{{ $topMatch->foundItem->title }}" 
                             class="w-16 h-16 rounded-xl object-cover shadow-sm border border-white">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-extrabold text-slate-900 text-base">{{ $topMatch->foundItem->title }}</h4>
                                <span class="px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-extrabold">
                                    Kecocokan Tinggi ({{ $topMatch->match_score }}%)
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-slate-400"></i> Ditemukan di: <strong>{{ $topMatch->foundItem->found_location }}</strong> &bull; Oleh: {{ $topMatch->foundItem->user?->name ?? 'Anonim' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('matches.show', $topMatch->id) }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-600/20 whitespace-nowrap transition-all flex items-center gap-1.5">
                        Lihat Detail
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            @else
                <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <i data-lucide="sparkles" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                    <p class="text-xs font-semibold text-slate-500">Belum ada kandidat kecocokan AI yang terdeteksi saat ini.</p>
                </div>
            @endif
        </div>

        <!-- Right: Quick Actions Card -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                <i data-lucide="zap" class="w-5 h-5 text-blue-600"></i>
                <h3 class="font-extrabold text-slate-900 text-lg">Quick Action</h3>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('lost-items.create') }}" class="p-4 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-200/80 hover:border-blue-200 transition-all text-center group">
                    <i data-lucide="search" class="w-6 h-6 mx-auto mb-2 text-rose-500 group-hover:scale-110 transition-transform"></i>
                    <span class="block text-xs font-extrabold">Laporkan Hilang</span>
                </a>
                <a href="{{ route('found-items.create') }}" class="p-4 rounded-2xl bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200/80 hover:border-emerald-200 transition-all text-center group">
                    <i data-lucide="plus-circle" class="w-6 h-6 mx-auto mb-2 text-emerald-500 group-hover:scale-110 transition-transform"></i>
                    <span class="block text-xs font-extrabold">Laporkan Ditemukan</span>
                </a>
                <a href="{{ route('claims.index') }}" class="p-4 rounded-2xl bg-slate-50 hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 border border-slate-200/80 hover:border-indigo-200 transition-all text-center group">
                    <i data-lucide="handshake" class="w-6 h-6 mx-auto mb-2 text-indigo-500 group-hover:scale-110 transition-transform"></i>
                    <span class="block text-xs font-extrabold">Lihat Semua Klaim</span>
                </a>
                <a href="{{ route('claims.index') }}" class="p-4 rounded-2xl bg-slate-50 hover:bg-amber-50 text-slate-700 hover:text-amber-700 border border-slate-200/80 hover:border-amber-200 transition-all text-center group">
                    <i data-lucide="clipboard-list" class="w-6 h-6 mx-auto mb-2 text-amber-500 group-hover:scale-110 transition-transform"></i>
                    <span class="block text-xs font-extrabold">Cek Status Klaim</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Section: Barang Terbaru -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-extrabold text-slate-900 text-xl flex items-center gap-2">
                    <i data-lucide="package" class="w-6 h-6 text-blue-600"></i>
                    Barang Terbaru
                </h3>
                <p class="text-xs text-slate-500 mt-1">Daftar laporan barang hilang dan ditemukan terbaru di lingkungan kampus.</p>
            </div>
            <a href="{{ route('public.found-items') }}" class="text-xs font-bold text-blue-600 hover:underline">
                Lihat Semua Barang &rarr;
            </a>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($recentLost as $item)
                <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden shadow-2xs hover:shadow-md transition-all">
                    <div class="relative h-40 bg-slate-200">
                        <img src="{{ $item->photos[0] ?? 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500&auto=format&fit=crop' }}" 
                             alt="{{ $item->title }}" class="w-full h-full object-cover">
                        <span class="absolute top-2.5 left-2.5 bg-rose-600 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full shadow-sm">
                            Hilang
                        </span>
                    </div>
                    <div class="p-4 space-y-2">
                        <h4 class="font-bold text-slate-900 text-sm truncate">{{ $item->title }}</h4>
                        <div class="text-xs text-slate-500 space-y-1">
                            <p><i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-slate-400"></i> {{ $item->last_seen_location }}</p>
                            <p><i data-lucide="calendar" class="w-3.5 h-3.5 inline text-slate-400"></i> {{ \Carbon\Carbon::parse($item->last_seen_date)->format('d M Y') }}</p>
                        </div>
                        <div class="pt-2">
                            <x-status-badge :status="$item->status" />
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach($recentFound as $item)
                <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden shadow-2xs hover:shadow-md transition-all">
                    <div class="relative h-40 bg-slate-200">
                        <img src="{{ $item->photos[0] ?? 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=500&auto=format&fit=crop' }}" 
                             alt="{{ $item->title }}" class="w-full h-full object-cover">
                        <span class="absolute top-2.5 left-2.5 bg-emerald-600 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full shadow-sm">
                            Ditemukan
                        </span>
                    </div>
                    <div class="p-4 space-y-2">
                        <h4 class="font-bold text-slate-900 text-sm truncate">{{ $item->title }}</h4>
                        <div class="text-xs text-slate-500 space-y-1">
                            <p><i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-slate-400"></i> {{ $item->found_location }}</p>
                            <p><i data-lucide="calendar" class="w-3.5 h-3.5 inline text-slate-400"></i> {{ \Carbon\Carbon::parse($item->found_date)->format('d M Y') }}</p>
                        </div>
                        <div class="pt-2 flex items-center justify-between">
                            <x-status-badge :status="$item->status" />
                            <a href="{{ route('public.found-items.show', $item->id) }}" class="text-xs font-bold text-blue-600 hover:underline">Detail</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
