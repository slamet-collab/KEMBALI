@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <!-- Staf Barang Operational Header -->
    <div class="rounded-3xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 p-8 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold uppercase border border-blue-400/30">
                    <i data-lucide="box" class="w-3.5 h-3.5"></i>
                    Manajemen Gudang & Logistik Barang Fisik
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">
                    Dashboard Staf Barang
                </h1>
                <p class="text-slate-300 text-sm">
                    Petugas: <strong>{{ $user->name }}</strong> &bull; Kelola pencatatan tempat penyimpanan, cetak QR Code, dan proses serah terima.
                </p>
            </div>
            <a href="{{ route('staff.storage') }}" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-2 whitespace-nowrap">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Catat Storage Baru
            </a>
        </div>
    </div>

    <!-- Stat Cards (4 Warehousing Stats) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $incomingCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Barang Masuk</span>
                    <span class="text-[11px] text-amber-600 font-semibold">Perlu dicatat rak</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i data-lucide="arrow-down-left" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $storedCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Barang Ditampung</span>
                    <span class="text-[11px] text-blue-600 font-semibold">Di Lemari Storage</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="archive" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $readyHandoverCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Siap Diambil</span>
                    <span class="text-[11px] text-teal-600 font-semibold">Klaim disetujui</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $completedHandoverCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Serah Terima Selesai</span>
                    <span class="text-[11px] text-emerald-600 font-semibold">Total dikembalikan</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="history" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Storage Items Location Table -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-900 text-lg flex items-center gap-2">
                <i data-lucide="map-pin" class="w-5 h-5 text-blue-600"></i>
                Daftar Lokasi Storage Barang Fisik Terbaru
            </h3>
            <a href="{{ route('staff.storage') }}" class="text-xs font-bold text-blue-600 hover:underline">
                Kelola Semua Storage &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-extrabold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">ID Storage / QR</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Lokasi Fisik (Gedung/Ruang/Lemari/Rak)</th>
                        <th class="px-4 py-3">Penerima Staf</th>
                        <th class="px-4 py-3">Tanggal Ditampung</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentStorages as $storage)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-blue-600">
                                <span class="px-2 py-1 rounded-md bg-blue-50 border border-blue-200">
                                    {{ $storage->storage_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $storage->foundItem?->title }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-slate-700">
                                <i data-lucide="building" class="w-3.5 h-3.5 inline text-slate-400"></i>
                                {{ $storage->building }} &rarr; {{ $storage->room }} &rarr; {{ $storage->cupboard }} ({{ $storage->shelf }})
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $storage->storedBy?->name ?? 'Staf Lost & Found' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $storage->received_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('staff.qr-scan') }}" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg border border-blue-200 inline-flex items-center gap-1">
                                    <i data-lucide="qr-code" class="w-3.5 h-3.5"></i> QR Code
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-400">Belum ada catatan lokasi storage fisik.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
