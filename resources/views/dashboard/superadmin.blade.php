@extends('layouts.app')

@section('content')

<div class="space-y-8">

    <!-- Super Admin Header Banner -->
    <div class="rounded-3xl bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 p-8 text-white shadow-xl">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 text-xs font-bold uppercase border border-purple-400/30">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-400"></i>
                Pusat Kendali Tingkat Tinggi System Administrator
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight">
                Dashboard Super Admin
            </h1>
            <p class="text-slate-300 text-sm">
                Super Admin: <strong>{{ $user->name }}</strong> &bull; Kontrol pengguna, pembuatan akun staf/admin, konfigurasi bobot AI, role permission, dan log aktivitas.
            </p>
        </div>
    </div>

    <!-- Stat Cards (4 Super Admin System Stats) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $totalUsers }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Total Pengguna Terdaftar</span>
                    <span class="text-[11px] text-blue-600 font-semibold">Seluruh Civitas</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $totalAdmins + $totalStaff }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Akun Admin & Staf</span>
                    <span class="text-[11px] text-sky-600 font-semibold">{{ $totalAdmins }} Admin &bull; {{ $totalStaff }} Staf</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                    <i data-lucide="user-check" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $totalCategories }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Kategori Barang</span>
                    <span class="text-[11px] text-purple-600 font-semibold">Aktif di sistem</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <i data-lucide="tag" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block">{{ $todayLogsCount }}</span>
                    <span class="text-xs font-bold text-slate-500 block mt-1">Activity Logs Hari Ini</span>
                    <span class="text-[11px] text-emerald-600 font-semibold">Sistem berjalan normal</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="activity" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- System Administration Quick Access Modules -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <a href="{{ route('superadmin.admins') }}" class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5 group">
            <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
            <h4 class="font-extrabold text-slate-900 text-base mb-1">Kelola Akun Admin & Staf</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Buat dan aktifkan akun Admin Operasional dan Staf Barang secara langsung.</p>
        </a>

        <a href="{{ route('superadmin.ai-config') }}" class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5 group">
            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide="sliders" class="w-6 h-6"></i>
            </div>
            <h4 class="font-extrabold text-slate-900 text-base mb-1">Konfigurasi AI Matching</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Atur bobot persentase kategori, warna, lokasi, waktu, dan deskripsi AI.</p>
        </a>

        <a href="{{ route('superadmin.logs') }}" class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5 group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <h4 class="font-extrabold text-slate-900 text-base mb-1">Audit Log Aktivitas</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Pantau seluruh catatan perubahan data, login, verifikasi, dan audit trail.</p>
        </a>

    </div>

    <!-- Recent Activity Log Audit Table -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-900 text-lg flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-purple-600"></i>
                Riwayat Activity Log Terbaru System
            </h3>
            <a href="{{ route('superadmin.logs') }}" class="text-xs font-bold text-purple-600 hover:underline">
                Lihat Seluruh Log &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-extrabold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">Aksi (Action)</th>
                        <th class="px-4 py-3">Deskripsi Aktivitas</th>
                        <th class="px-4 py-3">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-xs text-slate-500 font-mono">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">
                                {{ $log->user?->name ?? 'Sistem / Anonim' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-700">{{ $log->description }}</td>
                            <td class="px-4 py-3 text-xs font-mono text-slate-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-xs text-slate-400">Belum ada log aktivitas tercatat hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
