@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <!-- Formal Dosen Banner -->
    <div class="rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 p-8 text-white shadow-xl">
        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-blue-300 text-xs font-bold uppercase border border-white/20">
                <i data-lucide="award" class="w-3.5 h-3.5"></i>
                Portal Pelaporan Formal Dosen
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight">
                Selamat Datang, {{ $user->name }}
            </h1>
            <p class="text-slate-300 text-sm leading-relaxed">
                NIP: <strong>{{ $user->identity_no }}</strong> &bull; Portal ini memudahkan pengawasan barang inventaris, berkas tugas, maupun barang pribadi milik tenaga pendidik.
            </p>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $myReportsCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Total Laporan Saya</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $matchesCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Rekomendasi Kecocokan</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <i data-lucide="cpu" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $activeClaimsCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Klaim Aktif</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="clipboard-check" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Faculty Reports Table -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-900 text-lg flex items-center gap-2">
                <i data-lucide="list-filter" class="w-5 h-5 text-blue-600"></i>
                Riwayat Laporan Saya
            </h3>
            <a href="{{ route('lost-items.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all">
                + Buat Laporan Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-extrabold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Kode Barang</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Lokasi Terakhir</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($myReports as $item)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-blue-600">{{ $item->item_code }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $item->title }}</td>
                            <td class="px-4 py-3 text-xs">{{ $item->last_seen_location }}</td>
                            <td class="px-4 py-3 text-xs">{{ \Carbon\Carbon::parse($item->last_seen_date)->format('d M Y') }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$item->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-slate-400">Belum ada laporan barang yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
