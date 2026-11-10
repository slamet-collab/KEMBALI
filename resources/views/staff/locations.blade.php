@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <i data-lucide="map-pin" class="w-6 h-6 text-rose-500"></i>
            Lokasi Pos & Loker Penyimpanan
        </h1>
        <p class="text-xs text-slate-500 mt-1">Daftar gedung dan ruangan di lingkungan kampus yang digunakan sebagai titik penitipan dan loker barang hilang/temuan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($storages as $loc)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                    <i data-lucide="building" class="w-6 h-6"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-lg">{{ $loc->building }}</h3>
                <p class="text-sm font-semibold text-blue-600 mt-1 flex items-center gap-1.5">
                    <i data-lucide="door-closed" class="w-4 h-4"></i>
                    {{ $loc->room }}
                </p>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Titik Penitipan Resmi</span>
                    <span class="inline-flex items-center gap-1 text-emerald-600 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <i data-lucide="map-pin-off" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                <p class="text-sm font-semibold text-slate-500">Belum ada lokasi loker yang terdaftar.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
