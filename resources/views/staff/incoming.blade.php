@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="inbox" class="w-6 h-6 text-amber-500"></i>
                Barang Masuk ke Pos / Gudang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Barang temuan yang diserahkan pelapor ke Pos Satpam dan belum dimasukkan ke loker penyimpanan.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode & Foto</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Pelapor</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Lokasi Temuan</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Tanggal Masuk</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
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
                                    <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">{{ $item->item_code }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900 block">{{ $item->title }}</span>
                                <span class="text-xs text-slate-400">{{ $item->category?->name ?? 'Tanpa Kategori' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-slate-800 text-xs font-semibold block">{{ $item->user?->name ?? 'Anonim' }}</span>
                                <span class="text-slate-400 text-xs">{{ $item->user?->phone ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                {{ $item->location_found ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $item->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <button type="button" 
                                        onclick="openStoreModal({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ $item->item_code }}')" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                    <i data-lucide="archive" class="w-3.5 h-3.5"></i>
                                    Simpan di Loker
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i data-lucide="check-circle" class="w-10 h-10 text-emerald-400 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-600">Semua barang masuk sudah disimpan di loker/gudang.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $items->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal Simpan Barang ke Loker -->
<div id="storeModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                <i data-lucide="archive" class="w-5 h-5 text-blue-600"></i>
                Simpan ke Loker Penyimpanan
            </h3>
            <button onclick="closeStoreModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('staff.storage.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="found_item_id" id="modalFoundItemId">

            <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl">
                <p class="text-xs text-blue-800 font-semibold" id="modalItemInfo">Barang</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Gedung</label>
                    <input type="text" name="building" required placeholder="Gedung Rektorat" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ruangan</label>
                    <input type="text" name="room" required placeholder="Pos Satpam Utama" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Lemari / Loker</label>
                    <input type="text" name="cupboard" placeholder="Loker A" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Rak / Kotak</label>
                    <input type="text" name="shelf" placeholder="Rak 2" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Kondisi barang saat diterima..." class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeStoreModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openStoreModal(id, title, code) {
        document.getElementById('modalFoundItemId').value = id;
        document.getElementById('modalItemInfo').innerText = code + ' - ' + title;
        document.getElementById('storeModal').classList.remove('hidden');
    }
    function closeStoreModal() {
        document.getElementById('storeModal').classList.add('hidden');
    }
</script>
@endsection
