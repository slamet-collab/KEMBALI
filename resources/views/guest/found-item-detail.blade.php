@extends('layouts.guest')

@section('content')

    <!-- Top Navbar -->
    @include('components.navbar')

    <!-- Item Detail -->
    <section class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Breadcrumb / Back -->
            <a href="{{ route('public.found-items') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar Barang Ditemukan
            </a>

            <!-- Main Card -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Left: Image -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="relative h-80 bg-slate-100">
                            <img src="{{ $item->photos[0] ?? 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=800&auto=format&fit=crop' }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-full object-cover">
                            <span class="absolute top-4 left-4 bg-emerald-600 text-white text-[11px] font-extrabold px-3 py-1 rounded-full shadow-md">
                                DITEMUKAN
                            </span>
                            <span class="absolute top-4 right-4 bg-white/90 backdrop-blur-md text-slate-900 text-[11px] font-bold px-3 py-1 rounded-full shadow-sm border border-white/50">
                                {{ $item->item_code }}
                            </span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $item->title }}</h1>
                                @if($item->brand)
                                    <span class="text-sm text-slate-500 font-medium">{{ $item->brand }}</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 rounded-full bg-slate-100 text-xs font-bold text-slate-600 flex items-center gap-1.5">
                                    <i data-lucide="tag" class="w-3.5 h-3.5 text-slate-400"></i>
                                    {{ $item->category?->name }}
                                </span>
                                <span class="px-3 py-1 rounded-full bg-slate-100 text-xs font-bold text-slate-600 flex items-center gap-1.5">
                                    <i data-lucide="palette" class="w-3.5 h-3.5 text-slate-400"></i>
                                    {{ $item->primary_color }}
                                </span>
                            </div>
                            <x-status-badge :status="$item->status" />

                            @auth
                                @if(in_array($item->status, ['DIPUBLIKASIKAN', 'DITAMPUNG', 'MENUNGGU_KLAIM']))
                                    <a href="{{ route('claims.create', $item->id) }}" class="w-full mt-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2">
                                        <i data-lucide="handshake" class="w-4 h-4"></i>
                                        Ajukan Klaim Kepemilikan
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="w-full mt-2 px-5 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="log-in" class="w-4 h-4"></i>
                                    Login untuk Ajukan Klaim
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Right: Details -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Description -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                        <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                            <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                            Deskripsi & Kondisi Fisik
                        </h3>
                        <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $item->description }}</p>
                    </div>

                    <!-- Location & Time -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                        <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-5 h-5 text-emerald-500"></i>
                            Lokasi & Waktu Penemuan
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Lokasi</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $item->found_location }}</span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tanggal</span>
                                <span class="text-sm font-semibold text-slate-900">{{ \Carbon\Carbon::parse($item->found_date)->format('d M Y') }}</span>
                            </div>
                            @if($item->found_time)
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Waktu</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $item->found_time }} WIB</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Holding Info -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                        <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                            <i data-lucide="box" class="w-5 h-5 text-sky-500"></i>
                            Tempat Penyimpanan Saat Ini
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 bg-sky-50 rounded-2xl border border-sky-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Opsi</span>
                                <span class="text-sm font-semibold text-slate-900 capitalize">{{ str_replace('_', ' ', $item->holding_option) }}</span>
                            </div>
                            <div class="p-4 bg-sky-50 rounded-2xl border border-sky-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Lokasi Penyimpanan</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $item->holding_location_name ?? '-' }}</span>
                            </div>
                        </div>

                        @if($item->storage)
                            <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100 space-y-2">
                                <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block">📦 Dicatat di Gudang Kampus</span>
                                <div class="text-sm text-slate-700 space-y-0.5">
                                    <p><strong>Kode:</strong> {{ $item->storage->storage_code }}</p>
                                    <p><strong>Gedung:</strong> {{ $item->storage->building }}</p>
                                    <p><strong>Ruang:</strong> {{ $item->storage->room }}</p>
                                    <p><strong>Lemari/Rak:</strong> {{ $item->storage->cupboard }} — {{ $item->storage->shelf }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} <strong>KEMBALI — Campus Lost & Found</strong>. Universitas Pancasakti Tegal.</p>
        </div>
    </footer>

@endsection
