@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="boxes" class="w-6 h-6 text-blue-600"></i>
                Inventaris Penyimpanan & Loker
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar barang temuan yang tersimpan di loker fisik kampus dan siap diambil pemiliknya.</p>
        </div>
        <a href="{{ route('staff.incoming') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Barang Masuk Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode Loker</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Lokasi Fisik</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Petugas Penyimpan</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Waktu Simpan</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status Barang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($storages as $storage)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-extrabold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                                    {{ $storage->storage_code }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden flex items-center justify-center shrink-0">
                                        @if($storage->foundItem?->primary_photo_url)
                                            <img src="{{ asset('storage/' . $storage->foundItem->primary_photo_url) }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <i data-lucide="package" class="w-4 h-4 text-slate-400"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $storage->foundItem?->title ?? 'Barang' }}</span>
                                        <span class="text-xs text-slate-400 font-mono">{{ $storage->foundItem?->item_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs">
                                <span class="font-semibold text-slate-800 block">{{ $storage->building }} - {{ $storage->room }}</span>
                                <span class="text-slate-500">{{ $storage->cupboard ?? 'Loker Umum' }}, {{ $storage->shelf ?? 'Rak 1' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                {{ $storage->storedBy?->name ?? 'Petugas' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $storage->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <x-status-badge :status="$storage->foundItem?->status ?? 'MENUNGGU_VERIFIKASI'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i data-lucide="box" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Belum ada barang di loker penyimpanan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($storages->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $storages->links() }}</div>
        @endif
    </div>
</div>
@endsection
