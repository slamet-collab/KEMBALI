@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'found' }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="shield-alert" class="w-6 h-6 text-amber-500"></i>
                Verifikasi Laporan Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Laporan barang hilang langsung dipublikasikan. Periksa dan konfirmasi laporan barang ditemukan sebelum ditampilkan kepada publik.</p>
        </div>

        <!-- Tab Switcher -->
        <div class="flex bg-slate-200/80 p-1 rounded-xl text-xs font-bold">
            @if(Auth::user()->role?->code !== 'staf_barang')
            <button @click="tab = 'lost'" 
                    :class="tab === 'lost' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                <i data-lucide="help-circle" class="w-4 h-4"></i>
                Barang Hilang ({{ $lostItems->total() }})
            </button>
            @endif
            <button @click="tab = 'found'" 
                    :class="tab === 'found' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Barang Ditemukan ({{ $foundItems->total() }})
            </button>
        </div>
    </div>

    <!-- Tab Barang Hilang -->
    @if(Auth::user()->role?->code !== 'staf_barang')
    <div x-show="tab === 'lost'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode & Foto</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Judul & Kategori</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Pelapor</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Lokasi Hilang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Waktu Lapor</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lostItems as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                                        @if($item->primary_photo_url)
                                            <img src="{{ asset('storage/' . $item->primary_photo_url) }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <i data-lucide="package" class="w-5 h-5 text-slate-400"></i>
                                        @endif
                                    </div>
                                    <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">{{ $item->item_code }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('lost-items.show', $item->id) }}" class="font-bold text-slate-900 hover:text-blue-600 block">{{ $item->title }}</a>
                                <span class="text-xs text-slate-400">{{ $item->category?->name ?? 'Kategori Umum' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs">
                                <span class="font-bold text-slate-800 block">{{ $item->user?->name ?? 'User' }}</span>
                                <span class="text-slate-400">{{ $item->user?->identity_no ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                {{ $item->location_lost ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $item->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <form method="POST" action="{{ route('admin.verify-lost', $item->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                        Setujui
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.verify-lost', $item->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" onclick="return confirm('Tolak laporan ini?')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition-all">
                                        Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i data-lucide="check-circle" class="w-10 h-10 text-emerald-400 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada laporan barang hilang yang menunggu verifikasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lostItems->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $lostItems->links() }}</div>
        @endif
    </div>
    @endif

    <!-- Tab Barang Ditemukan -->
    <div x-show="tab === 'found'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode & Foto</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Judul & Kategori</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Pelapor</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Lokasi Ditemukan</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Waktu Lapor</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($foundItems as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                                        @if(!empty($item->photos[0]))
                                            <img src="{{ $item->photos[0] }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <i data-lucide="package" class="w-5 h-5 text-slate-400"></i>
                                        @endif
                                    </div>
                                    <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">{{ $item->item_code }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('found-items.show', $item->id) }}" class="font-bold text-slate-900 hover:text-emerald-600 block">{{ $item->title }}</a>
                                <span class="text-xs text-slate-400">{{ $item->category?->name ?? 'Kategori Umum' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs">
                                <span class="font-bold text-slate-800 block">{{ $item->user?->name ?? 'Anonim' }}</span>
                                <span class="text-slate-400">{{ $item->user?->identity_no ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                {{ $item->found_location ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $item->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <form method="POST" action="{{ route(Auth::user()->role?->code === 'staf_barang' ? 'staff.verify-found' : 'admin.verify-found', $item->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                        Setujui
                                    </button>
                                </form>
                                <form method="POST" action="{{ route(Auth::user()->role?->code === 'staf_barang' ? 'staff.verify-found' : 'admin.verify-found', $item->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" onclick="return confirm('Tolak laporan ini?')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition-all">
                                        Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i data-lucide="check-circle" class="w-10 h-10 text-emerald-400 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada laporan barang ditemukan yang menunggu verifikasi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($foundItems->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $foundItems->links() }}</div>
        @endif
    </div>
</div>
@endsection
