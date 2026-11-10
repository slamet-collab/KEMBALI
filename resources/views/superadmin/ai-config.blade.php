@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <i data-lucide="cpu" class="w-6 h-6 text-indigo-600"></i>
            Konfigurasi Engine AI Matching
        </h1>
        <p class="text-xs text-slate-500 mt-1">Pengaturan algoritma pencocokan cerdas antara laporan barang hilang dan temuan kampus.</p>
    </div>

    <form method="POST" action="{{ route('superadmin.ai-config.update') }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="sliders" class="w-5 h-5 text-indigo-600"></i>
                    Bobot Algoritma Kemiripan (Total 100%)
                </h2>
                <p class="text-xs text-slate-500 mt-1">Tentukan prioritas faktor yang menentukan persentase skor kecocokan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Bobot Kesamaan Judul & Brand (%)</label>
                    <input type="number" name="title_weight" value="35" min="0" max="100" class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <p class="text-[11px] text-slate-400">Pencocokan kata kunci nama barang dan merek.</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Bobot Kategori Barang (%)</label>
                    <input type="number" name="category_weight" value="25" min="0" max="100" class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <p class="text-[11px] text-slate-400">Pencocokan kategori yang sama persis.</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Bobot Lokasi Kampus (%)</label>
                    <input type="number" name="location_weight" value="20" min="0" max="100" class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <p class="text-[11px] text-slate-400">Kedekatan lokasi gedung atau area kehilangan/temuan.</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Bobot Rentang Tanggal / Waktu (%)</label>
                    <input type="number" name="date_weight" value="20" min="0" max="100" class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <p class="text-[11px] text-slate-400">Selisih waktu barang hilang dan barang ditemukan.</p>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Ambang Batas (Threshold) Notifikasi Otomatis</h3>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Skor Minimum untuk Notifikasi Pelapor (%)</label>
                    <input type="number" name="min_threshold" value="65" min="50" max="95" class="w-48 text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <p class="text-xs text-slate-400 mt-1">Jika skor kecocokan >= nilai ini, notifikasi otomatis dikirimkan ke pengguna pemilik barang hilang.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Konfigurasi AI
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
