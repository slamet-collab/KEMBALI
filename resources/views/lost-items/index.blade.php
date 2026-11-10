@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <!-- Success Notification Card for Newly Created Report -->
    @if(session('created_item'))
        @php($created = session('created_item'))
        <div class="p-6 rounded-3xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-xl shadow-emerald-500/20 border border-emerald-400 flex flex-col md:flex-row md:items-center justify-between gap-6 animate-fade-in">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold uppercase tracking-wider">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Laporan Berhasil Terkirim
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                    Laporan barang hilang berhasil dikirim!
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs">
                    <div class="bg-black/15 rounded-xl p-2.5 backdrop-blur-xs">
                        <span class="text-emerald-100 block text-[10px] uppercase font-bold">Nomor Laporan</span>
                        <span class="font-extrabold font-mono text-sm">{{ $created['item_code'] }}</span>
                    </div>
                    <div class="bg-black/15 rounded-xl p-2.5 backdrop-blur-xs">
                        <span class="text-emerald-100 block text-[10px] uppercase font-bold">Nama Barang</span>
                        <span class="font-bold truncate block">{{ $created['title'] }}</span>
                    </div>
                    <div class="bg-black/15 rounded-xl p-2.5 backdrop-blur-xs">
                        <span class="text-emerald-100 block text-[10px] uppercase font-bold">Tanggal Lapor</span>
                        <span class="font-bold">{{ $created['created_at'] }}</span>
                    </div>
                    <div class="bg-black/15 rounded-xl p-2.5 backdrop-blur-xs">
                        <span class="text-emerald-100 block text-[10px] uppercase font-bold">Status</span>
                        <span class="inline-flex items-center gap-1 font-extrabold {{ ($created['status'] ?? null) === 'DIPUBLIKASIKAN' ? 'text-emerald-200' : 'text-amber-200' }}">
                            <span class="w-2 h-2 rounded-full {{ ($created['status'] ?? null) === 'DIPUBLIKASIKAN' ? 'bg-emerald-300' : 'bg-amber-300' }} animate-pulse"></span>
                            {{ ($created['status'] ?? null) === 'DIPUBLIKASIKAN' ? 'Dipublikasikan' : 'Menunggu Verifikasi' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 self-start md:self-center shrink-0">
                <a href="{{ route('lost-items.show', $created['id']) }}" class="px-5 py-3 rounded-xl bg-white text-emerald-800 font-extrabold text-xs hover:bg-emerald-50 shadow-md transition-all flex items-center gap-2">
                    <i data-lucide="eye" class="w-4 h-4 text-emerald-600"></i>
                    Lihat Detail Laporan
                </a>
                <a href="{{ route('lost-items.index') }}" class="px-4 py-3 rounded-xl bg-emerald-700/60 hover:bg-emerald-700 text-white font-bold text-xs border border-white/20 transition-all flex items-center gap-2">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="search" class="w-6 h-6 text-rose-500"></i>
                Daftar & Pencarian Barang Hilang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Cari dan pantau laporan barang hilang di lingkungan kampus Universitas Pancasakti Tegal.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('lost-items.create') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-600/20 transition-all flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Buat Laporan Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
        <!-- Tabs: Semua Laporan vs Laporan Saya -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('lost-items.index') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request()->boolean('my_reports') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Barang Hilang
                </a>
                @auth
                    <a href="{{ route('lost-items.index', ['my_reports' => 1]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request()->boolean('my_reports') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        Laporan Saya
                        @if($myReportsCount > 0)
                            <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] {{ request()->boolean('my_reports') ? 'bg-white text-blue-700 font-extrabold' : 'bg-slate-300 text-slate-800' }}">
                                {{ $myReportsCount }}
                            </span>
                        @endif
                    </a>
                @endauth
            </div>
            <div class="text-xs text-slate-500 font-medium">
                Total ditemukan: <strong class="text-slate-900">{{ $items->total() }}</strong> laporan
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('lost-items.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            @if(request()->boolean('my_reports'))
                <input type="hidden" name="my_reports" value="1">
            @endif

            <!-- Keyword Search -->
            <div class="lg:col-span-4">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Kata Kunci</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama barang, kode, ciri, warna..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all font-medium">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="lg:col-span-3">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Kategori</label>
                <select name="category" class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all font-semibold">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Location Filter -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Lokasi</label>
                <input type="text" name="location" value="{{ request('location') }}" placeholder="cth: Perpustakaan"
                       class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all font-medium">
            </div>

            <!-- Date Filter -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Hilang</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all font-medium">
            </div>

            <!-- Action Buttons -->
            <div class="lg:col-span-1 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-1">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    Filter
                </button>
                @if(request()->hasAny(['q', 'category', 'location', 'date', 'status']))
                    <a href="{{ route('lost-items.index', request()->boolean('my_reports') ? ['my_reports' => 1] : []) }}" 
                       title="Reset Filter"
                       class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-all">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Items Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kategori & Warna</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Lokasi Kehilangan</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Pelapor</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-center px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-lg font-mono">{{ $item->item_code }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->photos[0] ?? 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=100&auto=format&fit=crop' }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                                    <div class="max-w-xs">
                                        <span class="font-bold text-slate-900 block text-sm leading-snug">{{ $item->title }}</span>
                                        @if($item->brand)
                                            <span class="text-[11px] text-slate-400 font-medium">{{ $item->brand }}</span>
                                        @endif
                                        <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $item->description }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs">
                                <span class="font-semibold text-slate-700 block">{{ $item->category?->name ?? '-' }}</span>
                                <span class="text-slate-400 text-[11px] flex items-center gap-1 mt-0.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    {{ $item->primary_color }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-rose-500 shrink-0"></i>
                                    <span class="font-medium">{{ $item->last_seen_location }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                <span class="font-semibold text-slate-900 block">{{ \Carbon\Carbon::parse($item->last_seen_date)->format('d M Y') }}</span>
                                @if($item->last_seen_time)
                                    <span class="text-slate-400 text-[11px]">{{ $item->last_seen_time }} WIB</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                @if(Auth::id() === $item->user_id)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[11px]">
                                        <i data-lucide="user" class="w-3 h-3"></i>
                                        Anda (Pelapor)
                                    </span>
                                @else
                                    <span class="font-medium text-slate-700">{{ $item->user?->name ?? 'Sivitas Kampus' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <x-status-badge :status="$item->status" />
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('lost-items.show', $item->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors">
                                        Detail
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                    @if(Auth::id() === $item->user_id || in_array(Auth::user()->role?->code, ['admin', 'super_admin', 'staf_barang']))
                                        @if($item->claims_count === 0)
                                            <form method="POST" action="{{ route('lost-items.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus laporan barang hilang ini? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 transition-colors">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="px-2 py-1.5 text-[11px] font-semibold text-slate-400" title="Laporan sudah memiliki klaim">Tidak dapat dihapus</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-14 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <i data-lucide="search-x" class="w-7 h-7"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800">Tidak ada laporan barang hilang ditemukan</h4>
                                        <p class="text-xs text-slate-500 mt-1">
                                            @if(request()->hasAny(['q', 'category', 'location', 'date', 'my_reports']))
                                                Tidak ada laporan yang cocok dengan kriteria filter pencarian Anda. Silakan coba atur ulang filter.
                                            @else
                                                Belum ada laporan barang hilang yang terdaftar saat ini.
                                            @endif
                                        </p>
                                    </div>
                                    <div class="pt-2 flex items-center justify-center gap-2">
                                        @if(request()->hasAny(['q', 'category', 'location', 'date', 'my_reports']))
                                            <a href="{{ route('lost-items.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                                                Reset Filter
                                            </a>
                                        @endif
                                        <a href="{{ route('lost-items.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-md shadow-blue-600/20">
                                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                            Laporkan Barang Hilang
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($items->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
