@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <!-- Back Button -->
    <a href="{{ route('lost-items.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Kembali ke Daftar
    </a>

    <!-- Main Detail Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Image & Basic Info -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <!-- Item Image -->
                <div class="relative h-72 bg-slate-100">
                    <img src="{{ $item->photos[0] ?? 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop' }}" 
                         alt="{{ $item->title }}" 
                         class="w-full h-full object-cover">
                    <span class="absolute top-4 left-4 bg-rose-600 text-white text-[11px] font-extrabold px-3 py-1 rounded-full shadow-md">
                        HILANG
                    </span>
                    <span class="absolute top-4 right-4 bg-white/90 backdrop-blur-md text-slate-900 text-[11px] font-bold px-3 py-1 rounded-full shadow-sm border border-white/50">
                        {{ $item->item_code }}
                    </span>
                </div>

                <!-- Item Quick Info -->
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
                        @if($item->claims_count === 0)
                            <form method="POST" action="{{ route('lost-items.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus laporan barang hilang ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full mt-2 px-5 py-3 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-sm transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    Hapus Laporan
                                </button>
                            </form>
                        @else
                            <p class="mt-2 text-xs font-semibold text-slate-500">Laporan tidak dapat dihapus karena sudah memiliki klaim terkait.</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Details -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Description Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                    Deskripsi & Ciri Khusus
                </h3>
                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $item->description }}</p>
            </div>

            <!-- Location & Time Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-5 h-5 text-rose-500"></i>
                    Lokasi & Waktu Terakhir Terlihat
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Lokasi</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $item->last_seen_location }}</span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tanggal</span>
                        <span class="text-sm font-semibold text-slate-900">{{ \Carbon\Carbon::parse($item->last_seen_date)->format('d M Y') }}</span>
                    </div>
                    @if($item->last_seen_time)
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Waktu</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $item->last_seen_time }} WIB</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Reporter Info Card -->
            @php
                $currentUser = Auth::user();
                $isOwnerOrStaff = $currentUser && ($currentUser->id === $item->user_id || in_array($currentUser->role?->code, ['admin', 'super_admin', 'staf_barang']));
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-indigo-500"></i>
                        Informasi Pelapor
                    </h3>
                    @if($isOwnerOrStaff)
                        <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 border border-blue-200">
                            <i data-lucide="shield-check" class="w-3 h-3"></i>
                            Akses Resmi Terverifikasi
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 font-semibold text-[10px] uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="shield" class="w-3 h-3"></i>
                            Privasi Dilindungi
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-600 text-white font-bold text-sm flex items-center justify-center shadow-md">
                        {{ strtoupper(substr($item->user?->name ?? 'AN', 0, 2)) }}
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 text-sm block">{{ $item->user?->name ?? 'Sivitas Kampus' }}</span>
                        <span class="text-xs text-slate-500">{{ $item->user?->role?->name ?? 'Pengguna' }}</span>
                    </div>
                </div>

                @if($isOwnerOrStaff)
                    <!-- Full Contact Info (Authorized only) -->
                    <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NIM / NIP</span>
                            <span class="font-semibold text-slate-800 font-mono">{{ $item->user?->identity_no ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Email</span>
                            <span class="font-semibold text-slate-800">{{ $item->user?->email ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 sm:col-span-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Telepon</span>
                            <span class="font-semibold text-slate-800 font-mono">{{ $item->user?->phone ?? 'Belum terdata' }}</span>
                        </div>
                    </div>
                @else
                    <!-- Protected Notice for Public / Other Users -->
                    <div class="pt-2 text-xs text-slate-400 flex items-center gap-1.5">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                        <span>Informasi kontak lengkap dan nomor identitas pelapor disembunyikan demi privasi.</span>
                    </div>
                @endif
            </div>

            <!-- AI Matches Section -->
            @if($item->matches && $item->matches->count() > 0)
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-3xl border border-amber-200/80 shadow-sm p-6 space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-5 h-5 text-amber-500"></i>
                        Kandidat Kecocokan AI ({{ $item->matches->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($item->matches as $match)
                            <div class="p-4 bg-white rounded-2xl border border-amber-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $match->foundItem?->photos[0] ?? '' }}" alt="" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                                    <div>
                                        <span class="font-bold text-sm text-slate-900 block">{{ $match->foundItem?->title }}</span>
                                        <span class="text-xs text-slate-500">{{ $match->foundItem?->found_location }}</span>
                                    </div>
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
