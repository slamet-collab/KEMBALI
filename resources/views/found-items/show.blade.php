@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <!-- Back Button -->
    <a href="{{ route('found-items.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-emerald-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali ke Daftar
    </a>

    <!-- Main Detail Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Image & Basic Info -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="relative h-72 bg-slate-100">
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

                    @if(Auth::id() === $item->user_id || in_array(Auth::user()->role?->code, ['admin', 'super_admin', 'staf_barang']))
                        @if($item->claims_count === 0 && $item->handover_records_count === 0 && !$item->storage)
                            <form method="POST" action="{{ route('found-items.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus laporan barang ditemukan ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full mt-2 px-5 py-3 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-sm transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    Hapus Laporan
                                </button>
                            </form>
                        @else
                            <p class="mt-2 text-xs font-semibold text-slate-500">Laporan tidak dapat dihapus karena sudah memiliki klaim atau catatan penyimpanan/serah terima.</p>
                        @endif
                    @endif

                    <!-- Claim Button -->
                    @auth
                        @if(in_array($item->status, ['DIPUBLIKASIKAN', 'DITAMPUNG', 'MENUNGGU_KLAIM']))
                            <a href="{{ route('claims.create', $item->id) }}" class="w-full mt-4 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="handshake" class="w-4 h-4"></i>
                                Ajukan Klaim Kepemilikan
                            </a>
                        @endif
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
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Lokasi Ditemukan</span>
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
                    Penyimpanan Barang
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-sky-50 rounded-2xl border border-sky-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Opsi Penyerahan</span>
                        <span class="text-sm font-semibold text-slate-900 capitalize">{{ str_replace('_', ' ', $item->holding_option) }}</span>
                    </div>
                    <div class="p-4 bg-sky-50 rounded-2xl border border-sky-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Lokasi Simpan</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $item->holding_location_name ?? '-' }}</span>
                    </div>
                </div>

                @if($item->storage)
                    <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100 space-y-2">
                        <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block">Sudah Dicatat di Gudang</span>
                        <div class="text-sm text-slate-700 space-y-0.5">
                            <p><strong>Kode:</strong> {{ $item->storage->storage_code }}</p>
                            <p><strong>Gedung:</strong> {{ $item->storage->building }}</p>
                            <p><strong>Ruang:</strong> {{ $item->storage->room }}</p>
                            <p><strong>Lemari/Rak:</strong> {{ $item->storage->cupboard }} — {{ $item->storage->shelf }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Reporter Info -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-indigo-500"></i>
                    Penemu
                </h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white font-bold text-sm flex items-center justify-center shadow-md">
                        {{ strtoupper(substr($item->user?->name ?? 'AN', 0, 2)) }}
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 text-sm block">{{ $item->user?->name ?? 'Anonim' }}</span>
                        <span class="text-xs text-slate-500">{{ $item->user?->identity_no }} &bull; {{ $item->user?->role?->name }}</span>
                    </div>
                </div>
            </div>

            <!-- AI Matches -->
            @if($item->matches && $item->matches->count() > 0)
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-3xl border border-amber-200/80 shadow-sm p-6 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-5 h-5 text-amber-500"></i>
                        Kandidat Kecocokan AI ({{ $item->matches->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($item->matches as $match)
                            <div class="p-4 bg-white rounded-2xl border border-amber-100 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-sm text-slate-900 block">{{ $match->lostItem?->title }}</span>
                                    <span class="text-xs text-slate-500">{{ $match->lostItem?->last_seen_location }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-extrabold text-blue-600">{{ $match->match_score }}%</span>
                                    <span class="block text-[10px] font-bold text-slate-400">{{ $match->confidence_level }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>

@endsection
