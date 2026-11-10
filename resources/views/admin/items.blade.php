@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'lost' }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="layers" class="w-6 h-6 text-blue-600"></i>
                Kelola Semua Barang Kampus
            </h1>
            <p class="text-xs text-slate-500 mt-1">Seluruh arsip data laporan barang hilang dan temuan di seluruh lingkungan universitas.</p>
        </div>

        <div class="flex bg-slate-200/80 p-1 rounded-xl text-xs font-bold">
            <button @click="tab = 'lost'" 
                    :class="tab === 'lost' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                <i data-lucide="help-circle" class="w-4 h-4"></i>
                Barang Hilang ({{ $lostItems->total() }})
            </button>
            <button @click="tab = 'found'" 
                    :class="tab === 'found' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                    class="px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Barang Ditemukan ({{ $foundItems->total() }})
            </button>
        </div>
    </div>

    <!-- Tabel Barang Hilang -->
    <div x-show="tab === 'lost'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Pelapor</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Lokasi Hilang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lostItems as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs font-bold text-blue-600">{{ $item->item_code }}</td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900 block">{{ $item->title }}</span>
                                <span class="text-xs text-slate-400">{{ $item->category?->name ?? 'Umum' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-700">{{ $item->user?->name ?? 'User' }}</td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">{{ $item->location_lost ?? '-' }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :status="$item->status" /></td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('lost-items.show', $item->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada barang hilang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lostItems->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $lostItems->links() }}</div>
        @endif
    </div>

    <!-- Tabel Barang Ditemukan -->
    <div x-show="tab === 'found'" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Penemu</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Lokasi Temuan</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($foundItems as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs font-bold text-emerald-600">{{ $item->item_code }}</td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900 block">{{ $item->title }}</span>
                                <span class="text-xs text-slate-400">{{ $item->category?->name ?? 'Umum' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-700">{{ $item->user?->name ?? 'Anonim' }}</td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">{{ $item->location_found ?? '-' }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :status="$item->status" /></td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('found-items.show', $item->id) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada barang ditemukan.</td>
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
