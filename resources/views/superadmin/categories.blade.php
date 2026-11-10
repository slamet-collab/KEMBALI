@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="tag" class="w-6 h-6 text-pink-600"></i>
                Kelola Kategori Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar klasifikasi kategori barang hilang dan temuan untuk mempermudah pencarian dan AI matching.</p>
        </div>
        <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Kategori
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center">
                        <i data-lucide="{{ $category->icon ?: 'tag' }}" class="w-6 h-6"></i>
                    </div>
                    <span class="text-xs font-mono text-slate-400 bg-slate-50 px-2.5 py-1 rounded-md">{{ $category->slug }}</span>
                </div>

                <h3 class="text-lg font-extrabold text-slate-900">{{ $category->name }}</h3>
                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $category->description ?? 'Deskripsi kategori barang.' }}</p>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-blue-600 font-bold flex items-center gap-1">
                        <i data-lucide="help-circle" class="w-3.5 h-3.5"></i>
                        {{ $category->lost_items_count }} Hilang
                    </span>
                    <span class="text-emerald-600 font-bold flex items-center gap-1">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        {{ $category->found_items_count }} Temuan
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-2xl border border-slate-200 p-12 text-center text-sm text-slate-500">
                Belum ada kategori yang dibuat.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div id="addCategoryModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-pink-600"></i>
                Tambah Kategori Baru
            </h3>
            <button onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('superadmin.categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Kategori</label>
                <input type="text" name="name" required placeholder="Contoh: Elektronik & Gadget" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Icon (Nama Lucide Icon)</label>
                <input type="text" name="icon" placeholder="laptop, smartphone, wallet, key..." class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi Singkat</label>
                <textarea name="description" rows="2" placeholder="Penjelasan barang dalam kategori ini..." class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-pink-600 hover:bg-pink-700 rounded-xl transition-colors">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>
@endsection
