@extends('layouts.guest')

@section('content')

    <!-- Top Navbar -->
    @include('components.navbar')

    <!-- Hero Section -->
    <section class="relative pt-12 pb-20 overflow-hidden bg-gradient-to-b from-blue-50/50 via-slate-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Hero Text Left -->
                <div class="lg:col-span-7 space-y-6 text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100/80 border border-blue-200 text-blue-700 text-xs font-bold tracking-wider uppercase">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-blue-600"></i>
                        CAMPUS LOST & FOUND
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Temukan. Cocokkan.<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500">
                            Kembalikan.
                        </span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 font-normal leading-relaxed max-w-2xl">
                        KEMBALI hadir untuk membantu mahasiswa, dosen, dan seluruh civitas akademika menemukan kembali barang hilang dan mengembalikan barang yang tertinggal di lingkungan kampus secara aman dan terverifikasi.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('lost-items.create') }}" class="px-7 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-base shadow-xl shadow-blue-600/30 hover:shadow-blue-600/40 hover:-translate-y-0.5 transition-all flex items-center gap-2.5">
                            <i data-lucide="search" class="w-5 h-5"></i>
                            Laporkan Barang Hilang
                        </a>
                        <a href="{{ route('found-items.create') }}" class="px-7 py-3.5 rounded-2xl bg-white hover:bg-slate-50 text-blue-600 font-bold text-base border-2 border-blue-600/30 hover:border-blue-600 transition-all flex items-center gap-2.5">
                            <i data-lucide="plus-circle" class="w-5 h-5"></i>
                            Laporkan Barang Ditemukan
                        </a>
                    </div>
                </div>

                <!-- Hero Image Right with Floating Badges -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Main Hero Image Frame -->
                        <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&auto=format&fit=crop" 
                                 alt="Mahasiswa Kampus" 
                                 class="w-full h-[400px] object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                            
                            <!-- Campus Name Tag Overlay -->
                            <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/50 text-xs font-bold text-slate-900 shadow-md flex items-center gap-2">
                                <i data-lucide="building-2" class="w-4 h-4 text-blue-600"></i>
                                Universitas Pancasakti Tegal
                            </div>
                        </div>

                        <!-- Floating Badge 1 Top Right -->
                        <div class="absolute -top-6 -right-6 bg-white p-3.5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3 animate-bounce-slow">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                <i data-lucide="laptop" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-900">Laptop Ditemukan</span>
                                <span class="block text-[11px] font-semibold text-slate-500">di Gedung B &bull; <span class="text-emerald-600 font-bold">Cocok 98%</span></span>
                            </div>
                        </div>

                        <!-- Floating Badge 2 Bottom Left -->
                        <div class="absolute -bottom-6 -left-6 bg-white p-3.5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                <i data-lucide="wallet" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-900">Dompet Hilang</span>
                                <span class="block text-[11px] font-semibold text-slate-500">di Perpustakaan &bull; <span class="text-blue-600 font-bold">Dalam Proses</span></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Value Propositions Strip -->
    <section class="py-8 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                
                <div class="flex items-center gap-3.5 p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Terpercaya & Aman</h4>
                        <p class="text-xs text-slate-500">Data terlindungi dengan sistem verifikasi admin.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="cpu" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Didukung AI</h4>
                        <p class="text-xs text-slate-500">Mencocokkan barang dengan tingkat akurasi tinggi.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="bell" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Notifikasi Real-time</h4>
                        <p class="text-xs text-slate-500">Pemberitahuan instant saat barang ditemukan.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3.5 p-3 rounded-2xl hover:bg-slate-50 transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Untuk Seluruh Civitas</h4>
                        <p class="text-xs text-slate-500">Mahasiswa, dosen, dan staf kampus terhubung.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- How It Works Section (3 Simple Steps) -->
    <section id="cara-kerja" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <span class="text-xs font-extrabold uppercase tracking-wider text-blue-600 block mb-2">BAGAIMANA CARA KERJANYA?</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                Hanya 3 langkah sederhana untuk menemukan barang Anda.
            </h2>
            <p class="text-slate-600 max-w-2xl mx-auto mb-16 text-sm sm:text-base">
                Laporan barang hilang atau ditemukan biarkan AI kami mencocokkan dan membantu mengembalikannya ke pemilik sah.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                
                <!-- Step 01 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 text-center hover:shadow-md transition-shadow relative">
                    <div class="w-16 h-16 rounded-2xl bg-blue-600 text-white font-extrabold text-xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-500/30">
                        01
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900 mb-2">Laporkan Barang</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Isi formulir laporan barang hilang atau ditemukan, lengkapi dengan foto dan detail ciri khusus.
                    </p>
                </div>

                <!-- Step 02 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 text-center hover:shadow-md transition-shadow relative">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white font-extrabold text-xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-500/30">
                        02
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900 mb-2">AI Cocokkan</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Sistem akan menganalisis data dan mencari kemungkinan kecocokan otomatis dengan bantuan AI Engine.
                    </p>
                </div>

                <!-- Step 03 -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 text-center hover:shadow-md transition-shadow relative">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white font-extrabold text-xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/30">
                        03
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900 mb-2">Kembalikan</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Pengguna mengajukan klaim, verifikasi admin disetujui, dan staf melakukan serah terima barang.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- Latest Items Preview Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                        <i data-lucide="package" class="w-7 h-7 text-blue-600"></i>
                        Barang Terbaru
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Lihat beberapa laporan barang hilang dan ditemukan terbaru dari kampus.</p>
                </div>
                <a href="{{ route('public.found-items') }}" class="text-blue-600 hover:text-blue-700 font-bold text-sm flex items-center gap-1">
                    Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- Items Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                @php
                    $latestLost = App\Models\LostItem::with('category')->latest()->take(2)->get();
                    $latestFound = App\Models\FoundItem::with('category')->where('is_public', true)->latest()->take(2)->get();
                @endphp

                @foreach($latestLost as $item)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all group">
                        <div class="relative h-48 bg-slate-100 overflow-hidden">
                            <img src="{{ $item->photos[0] ?? 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500&auto=format&fit=crop' }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <span class="absolute top-3 left-3 bg-rose-500 text-white text-[11px] font-extrabold px-3 py-1 rounded-full shadow-md">
                                HILANG
                            </span>
                        </div>
                        <div class="p-5 space-y-2">
                            <span class="text-xs font-semibold text-slate-400 block">{{ $item->category?->name }}</span>
                            <h4 class="font-bold text-slate-900 text-base truncate">{{ $item->title }}</h4>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $item->last_seen_location }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <span>{{ \Carbon\Carbon::parse($item->last_seen_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                @foreach($latestFound as $item)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all group">
                        <div class="relative h-48 bg-slate-100 overflow-hidden">
                            <img src="{{ $item->photos[0] ?? 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=500&auto=format&fit=crop' }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <span class="absolute top-3 left-3 bg-emerald-500 text-white text-[11px] font-extrabold px-3 py-1 rounded-full shadow-md">
                                DITEMUKAN
                            </span>
                        </div>
                        <div class="p-5 space-y-2">
                            <span class="text-xs font-semibold text-slate-400 block">{{ $item->category?->name }}</span>
                            <h4 class="font-bold text-slate-900 text-base truncate">{{ $item->title }}</h4>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $item->found_location }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($item->found_date)->format('d M Y') }}</span>
                                <a href="{{ route('public.found-items.show', $item->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                                    Detail &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <div class="flex items-center justify-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <span class="font-extrabold text-lg text-white">KEMBALI — Campus Lost & Found</span>
            </div>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Platform resmi manajemen barang hilang & ditemukan Universitas Pancasakti Tegal. Temukan, cocokkan, dan kembalikan dengan jujur.
            </p>
            <p class="text-xs text-slate-600 pt-4">&copy; {{ date('Y') }} KEMBALI. All rights reserved.</p>
        </div>
    </footer>

@endsection
