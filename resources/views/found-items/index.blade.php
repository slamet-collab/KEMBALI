@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="package-search" class="w-6 h-6 text-emerald-500"></i>
                Daftar Barang Ditemukan
            </h1>
            <p class="text-xs text-slate-500 mt-1">Semua laporan barang ditemukan di lingkungan kampus.</p>
        </div>
        <a href="{{ route('found-items.create') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2 self-start">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Laporkan Temuan Baru
        </a>
    </div>

    <!-- Items Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Lokasi Ditemukan</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Penemu</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-center px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">{{ $item->item_code }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->photos[0] ?? 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=100&auto=format&fit=crop' }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                                    <div>
                                        <span class="font-semibold text-slate-900 block text-sm">{{ $item->title }}</span>
                                        @if($item->brand)
                                            <span class="text-xs text-slate-400">{{ $item->brand }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">{{ $item->category?->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                <div class="flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                    {{ $item->found_location }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">{{ \Carbon\Carbon::parse($item->found_date)->format('d M Y') }}</td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">{{ $item->user?->name ?? 'Anonim' }}</td>
                            <td class="px-5 py-3.5">
                                <x-status-badge :status="$item->status" />
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('found-items.show', $item->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                                        Detail &rarr;
                                    </a>
                                    @if(Auth::id() === $item->user_id || in_array(Auth::user()->role?->code, ['admin', 'super_admin', 'staf_barang']))
                                        @if($item->claims_count === 0 && $item->handover_records_count === 0 && !$item->storage)
                                            <form method="POST" action="{{ route('found-items.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus laporan barang ditemukan ini? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 transition-colors">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] font-semibold text-slate-400" title="Laporan sudah memiliki klaim atau catatan penyimpanan/serah terima">Tidak dapat dihapus</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Belum ada laporan barang ditemukan.</p>
                                <a href="{{ route('found-items.create') }}" class="text-xs text-emerald-600 font-bold hover:underline mt-1 inline-block">Laporkan temuan pertama &rarr;</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
