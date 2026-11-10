@extends('layouts.guest')

@section('content')

    <!-- Top Navbar -->
    @include('components.navbar')

    <!-- Page Hero Banner -->
    <section class="bg-gradient-to-b from-blue-50/80 to-white py-12 border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                <i data-lucide="package-search" class="w-8 h-8 text-emerald-500 inline"></i>
                Papan Barang Ditemukan
            </h1>
            <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto">
                Telusuri daftar barang yang ditemukan di lingkungan kampus Universitas Pancasakti Tegal. Silakan cari dan ajukan klaim jika Anda adalah pemiliknya.
            </p>
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section class="bg-white border-b border-slate-200/80 py-5 sticky top-0 z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('public.found-items') }}" class="flex flex-wrap items-end gap-3">
                
                <!-- Search Keyword -->
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Barang</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama, deskripsi, lokasi, warna..."
                               class="w-full pl-9 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="w-44">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kategori</label>
                    <select name="category" class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
                        <option value="">Semua</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div class="w-44">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Lokasi</label>
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="cth: Gedung A"
                           class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
                </div>

                <!-- Date Filter -->
                <div class="w-40">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ request('date') }}"
                           class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
                </div>

                <!-- Submit -->
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition-all flex items-center gap-1.5">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filter
                </button>

                @if(request()->hasAny(['q', 'category', 'location', 'date', 'color']))
                    <a href="{{ route('public.found-items') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-sm rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </section>

    <!-- Items Grid -->
    <section class="py-10 bg-slate-50 min-h-[60vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-slate-500">
                    Menampilkan <strong class="text-slate-900">{{ $items->total() }}</strong> barang ditemukan
                </p>
            </div>

            @if($items->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($items as $item)
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all group">
                            <div class="relative h-48 bg-slate-100 overflow-hidden">
                                <img src="{{ $item->photos[0] ?? 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=500&auto=format&fit=crop' }}" 
                                     alt="{{ $item->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <span class="absolute top-3 left-3 bg-emerald-500 text-white text-[11px] font-extrabold px-3 py-1 rounded-full shadow-md">
                                    DITEMUKAN
                                </span>
                                <span class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded-lg shadow-sm">
                                    {{ $item->item_code }}
                                </span>
                            </div>
                            <div class="p-5 space-y-2.5">
                                <span class="text-xs font-semibold text-slate-400 block">{{ $item->category?->name }}</span>
                                <h4 class="font-bold text-slate-900 text-base truncate">{{ $item->title }}</h4>
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>{{ $item->found_location }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <span>{{ \Carbon\Carbon::parse($item->found_date)->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                    <x-status-badge :status="$item->status" />
                                    <a href="{{ route('public.found-items.show', $item->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                                        Detail &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $items->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <i data-lucide="search-x" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
                    <h3 class="text-lg font-bold text-slate-600">Tidak ditemukan barang yang cocok</h3>
                    <p class="text-sm text-slate-500 mt-1">Coba ubah filter pencarian atau gunakan kata kunci lain.</p>
                </div>
            @endif

        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} <strong>KEMBALI — Campus Lost & Found</strong>. Universitas Pancasakti Tegal.</p>
        </div>
    </footer>

@endsection
