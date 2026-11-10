@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <!-- Admin Header Banner -->
    <div class="rounded-3xl bg-gradient-to-r from-indigo-900 via-blue-900 to-slate-900 p-8 text-white shadow-xl">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold uppercase border border-indigo-400/30">
                <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i>
                Pusat Kontrol & Verifikasi Admin Operasional
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight">
                Dashboard Admin Operasional
            </h1>
            <p class="text-slate-300 text-sm">
                Admin: <strong>{{ $user->name }}</strong> &bull; Verifikasi laporan baru, tinjau bukti kepemilikan klaim, dan awasi pencocokan AI.
            </p>
        </div>
    </div>

    <!-- Stat Cards (4 Admin Stats) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $pendingReportVerifications }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Barang Ditemukan Menunggu Konfirmasi</span>
                    <span class="text-[11px] text-amber-600 font-semibold">Butuh pemeriksaan</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i data-lucide="file-search" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $pendingClaimsVerifications }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Tinjauan Klaim Aktif</span>
                    <span class="text-[11px] text-blue-600 font-semibold">Periksa bukti kepemilikan</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="check-square" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $highConfidenceMatches }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Pencocokan AI High-Score</span>
                    <span class="text-[11px] text-purple-600 font-semibold">Match score &ge; 85%</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <i data-lucide="sparkles" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $totalHandovers }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Total Serah Terima</span>
                    <span class="text-[11px] text-emerald-600 font-semibold">Barang kembali sukses</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Verification Pending Claims Table -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-900 text-lg flex items-center gap-2">
                <i data-lucide="handshake" class="w-5 h-5 text-indigo-600"></i>
                Pengajuan Klaim Kepemilikan Perlu Ditinjau
            </h3>
            <a href="{{ route('admin.claims') }}" class="text-xs font-bold text-blue-600 hover:underline">
                Kelola Semua Klaim &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-extrabold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Kode Klaim</th>
                        <th class="px-4 py-3">Pengaju (Mahasiswa/Dosen)</th>
                        <th class="px-4 py-3">Barang Ditemukan</th>
                        <th class="px-4 py-3">Bukti Kepemilikan & Ciri Khusus</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($unverifiedClaims as $claim)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-indigo-600">{{ $claim->claim_code }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">
                                {{ $claim->user?->name }}
                                <span class="block text-[11px] font-medium text-slate-400">NIM: {{ $claim->user?->identity_no }}</span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $claim->foundItem?->title }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600 max-w-xs truncate">
                                {{ $claim->ownership_proof }}
                            </td>
                            <td class="px-4 py-3"><x-status-badge :status="$claim->status" /></td>
                            <td class="px-4 py-3 text-center space-x-1">
                                <a href="{{ route('admin.claims.show', $claim->id) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all inline-flex items-center gap-1">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Periksa Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-400">Tidak ada pengajuan klaim baru yang menunggu verifikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
