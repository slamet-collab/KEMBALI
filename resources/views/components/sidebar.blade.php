@php
    $user = Auth::user();
    $roleCode = $user?->role?->code ?? 'mahasiswa';
    $routeName = Route::currentRouteName();

    $isDashboardActive = str_starts_with($routeName ?? '', 'dashboard.');
    $isLostActive = str_starts_with($routeName ?? '', 'lost-items.');
    $isFoundActive = str_starts_with($routeName ?? '', 'found-items.');
    $isMatchesActive = str_starts_with($routeName ?? '', 'matches.') || $routeName === 'admin.ai-matches';
    $isClaimsActive = str_starts_with($routeName ?? '', 'claims.') || $routeName === 'admin.claims';
    $isNotifActive = str_starts_with($routeName ?? '', 'notifications.');

    $navBaseClass = 'flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 active:scale-95 cursor-pointer';
    $navInactiveClass = 'text-slate-300 hover:bg-slate-800 hover:text-white hover:translate-x-1 hover:shadow-md';
    $navActiveClass = 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30';
    $navSuperAdminActiveClass = 'bg-purple-600 text-white font-bold shadow-lg shadow-purple-600/30';
@endphp

<aside x-data="{ open: false }" 
       class="bg-slate-900 text-slate-300 w-64 min-h-screen flex flex-col fixed left-0 top-0 bottom-0 z-40 transition-all duration-300 shadow-xl border-r border-slate-800">
    
    <!-- Brand Logo Header -->
    <div class="p-5 flex items-center justify-between border-b border-slate-800/80">
        <a href="{{ route('landing') }}" class="flex items-center gap-3 group transition-transform duration-200 active:scale-95">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 group-hover:shadow-blue-500/50 transition-all duration-200">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="font-extrabold text-lg tracking-tight text-white block leading-tight group-hover:text-blue-400 transition-colors">KEMBALI</span>
                <span class="text-[10px] text-slate-400 tracking-wider font-semibold uppercase block">Campus Lost & Found</span>
            </div>
        </a>
    </div>

    <!-- User Role Info Card -->
    <div class="px-4 py-3 mx-3 mt-4 rounded-xl bg-slate-800/60 border border-slate-700/50 flex items-center gap-3 hover:bg-slate-800/90 transition-colors">
        <div class="w-9 h-9 rounded-full bg-blue-600/20 text-blue-400 flex items-center justify-center font-bold text-sm border border-blue-500/30">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div class="overflow-hidden">
            <h4 class="text-sm font-semibold text-white truncate">{{ $user->name }}</h4>
            <span class="inline-block px-2 py-0.5 text-[11px] font-medium rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                {{ $user->role?->name }}
            </span>
        </div>
    </div>

    <!-- Navigation Menu Items based on Role -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        <!-- 1. MAHASISWA MENU -->
        @if($roleCode === 'mahasiswa')
            <a href="{{ route('dashboard.mahasiswa') }}" 
               class="{{ $navBaseClass }} {{ $isDashboardActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Dashboard
            </a>
            <a href="{{ route('lost-items.index') }}" 
               class="{{ $navBaseClass }} {{ $isLostActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="search" class="w-5 h-5"></i>
                Barang Hilang
            </a>
            <a href="{{ route('found-items.index') }}" 
               class="{{ $navBaseClass }} {{ $isFoundActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="package-search" class="w-5 h-5"></i>
                Barang Ditemukan
            </a>
            <a href="{{ route('matches.index') }}" 
               class="{{ $navBaseClass }} {{ $isMatchesActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i>
                Kandidat Kecocokan AI
            </a>
            <a href="{{ route('claims.index') }}" 
               class="{{ $navBaseClass }} {{ $isClaimsActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="handshake" class="w-5 h-5"></i>
                Klaim Saya
            </a>
            <a href="{{ route('notifications.index') }}" 
               class="{{ $navBaseClass }} {{ $isNotifActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="bell" class="w-5 h-5"></i>
                Notifikasi
            </a>

        <!-- 2. DOSEN MENU -->
        @elseif($roleCode === 'dosen')
            <a href="{{ route('dashboard.dosen') }}" 
               class="{{ $navBaseClass }} {{ $isDashboardActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Dashboard Dosen
            </a>
            <a href="{{ route('lost-items.index') }}" 
               class="{{ $navBaseClass }} {{ $isLostActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                Laporan Saya
            </a>
            <a href="{{ route('found-items.index') }}" 
               class="{{ $navBaseClass }} {{ $isFoundActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="package-check" class="w-5 h-5"></i>
                Barang Ditemukan
            </a>
            <a href="{{ route('matches.index') }}" 
               class="{{ $navBaseClass }} {{ $isMatchesActive ? $navActiveClass : $navInactiveClass }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="cpu" class="w-5 h-5 text-indigo-400"></i>
                    Kandidat AI
                </div>
            </a>
            <a href="{{ route('claims.index') }}" 
               class="{{ $navBaseClass }} {{ $isClaimsActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                Klaim Saya
            </a>
            <a href="{{ route('notifications.index') }}" 
               class="{{ $navBaseClass }} {{ $isNotifActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="bell" class="w-5 h-5"></i>
                Notifikasi
            </a>

        <!-- 3. STAF BARANG MENU -->
        @elseif($roleCode === 'staf_barang')
            <a href="{{ route('dashboard.staf') }}" 
               class="{{ $navBaseClass }} {{ $isDashboardActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Dashboard Staf
            </a>
            <a href="{{ route('staff.verifications') }}"
               class="{{ $navBaseClass }} {{ $routeName === 'staff.verifications' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="shield-alert" class="w-5 h-5 text-amber-400"></i>
                Konfirmasi Barang Ditemukan
            </a>
            <a href="{{ route('staff.incoming') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'staff.incoming' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="arrow-down-left" class="w-5 h-5 text-emerald-400"></i>
                Barang Masuk
            </a>
            <a href="{{ route('staff.storage') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'staff.storage' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="box" class="w-5 h-5 text-sky-400"></i>
                Barang Ditampung
            </a>
            <a href="{{ route('staff.handover') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'staff.handover' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="arrow-up-right" class="w-5 h-5 text-indigo-400"></i>
                Barang Keluar
            </a>
            <a href="{{ route('staff.locations') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'staff.locations' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="map-pin" class="w-5 h-5 text-amber-400"></i>
                Lokasi Penyimpanan
            </a>
            <a href="{{ route('staff.qr-scan') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'staff.qr-scan' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="qr-code" class="w-5 h-5 text-purple-400"></i>
                Scan QR Code
            </a>
            <a href="{{ route('staff.handover-history') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'staff.handover-history' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="history" class="w-5 h-5"></i>
                Riwayat Serah Terima
            </a>

        <!-- 4. ADMIN MENU -->
        @elseif($roleCode === 'admin')
            <a href="{{ route('dashboard.admin') }}" 
               class="{{ $navBaseClass }} {{ $isDashboardActive ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                Dashboard Admin
            </a>
            <a href="{{ route('admin.verifications') }}" 
               class="{{ $navBaseClass }} justify-between {{ $routeName === 'admin.verifications' ? $navActiveClass : $navInactiveClass }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="shield-alert" class="w-5 h-5 text-amber-400"></i>
                    Verifikasi Laporan
                </div>
            </a>
            <a href="{{ route('admin.claims') }}" 
               class="{{ $navBaseClass }} justify-between {{ $routeName === 'admin.claims' ? $navActiveClass : $navInactiveClass }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-square" class="w-5 h-5 text-emerald-400"></i>
                    Kelola Klaim
                </div>
            </a>
            <a href="{{ route('admin.ai-matches') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'admin.ai-matches' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="sparkles" class="w-5 h-5 text-indigo-400"></i>
                Kandidat AI
            </a>
            <a href="{{ route('admin.items') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'admin.items' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="layers" class="w-5 h-5"></i>
                Kelola Barang
            </a>
            <a href="{{ route('admin.users') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'admin.users' ? $navActiveClass : $navInactiveClass }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                Pengguna Kampus
            </a>

        <!-- 5. SUPER ADMIN MENU -->
        @elseif($roleCode === 'super_admin')
            <a href="{{ route('dashboard.superadmin') }}" 
               class="{{ $navBaseClass }} {{ $isDashboardActive ? $navSuperAdminActiveClass : $navInactiveClass }}">
                <i data-lucide="shield-check" class="w-5 h-5 text-amber-400"></i>
                Dashboard Super Admin
            </a>
            <a href="{{ route('superadmin.admins') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'superadmin.admins' ? $navSuperAdminActiveClass : $navInactiveClass }}">
                <i data-lucide="user-check" class="w-5 h-5 text-sky-400"></i>
                Kelola Admin
            </a>
            <a href="{{ route('superadmin.staff') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'superadmin.staff' ? $navSuperAdminActiveClass : $navInactiveClass }}">
                <i data-lucide="user-cog" class="w-5 h-5 text-emerald-400"></i>
                Kelola Staf Barang
            </a>
            <a href="{{ route('superadmin.users') }}"
               class="{{ $navBaseClass }} {{ $routeName === 'superadmin.users' ? $navSuperAdminActiveClass : $navInactiveClass }}">
                <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
                Kelola Pengguna & Role
            </a>
            <a href="{{ route('superadmin.roles') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'superadmin.roles' ? $navSuperAdminActiveClass : $navInactiveClass }}">
                <i data-lucide="lock" class="w-5 h-5 text-amber-400"></i>
                Role & Permission
            </a>
            <a href="{{ route('superadmin.categories') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'superadmin.categories' ? $navSuperAdminActiveClass : $navInactiveClass }}">
                <i data-lucide="tag" class="w-5 h-5 text-rose-400"></i>
                Kategori Barang
            </a>
            <a href="{{ route('superadmin.ai-config') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'superadmin.ai-config' ? $navSuperAdminActiveClass : $navInactiveClass }}">
                <i data-lucide="sliders" class="w-5 h-5 text-indigo-400"></i>
                Konfigurasi AI
            </a>
            <a href="{{ route('superadmin.logs') }}" 
               class="{{ $navBaseClass }} {{ $routeName === 'superadmin.logs' ? $navSuperAdminActiveClass : $navInactiveClass }}">
                <i data-lucide="activity" class="w-5 h-5 text-teal-400"></i>
                Activity Log
            </a>
        @endif

        <div class="pt-4 pb-2 border-t border-slate-800/80 my-2">
            <span class="px-3 text-[11px] font-bold tracking-wider uppercase text-slate-500">Akun Saya</span>
        </div>

        <a href="{{ route('profile.show') }}" 
           class="{{ $navBaseClass }} {{ $routeName === 'profile.show' ? $navActiveClass : $navInactiveClass }}">
            <i data-lucide="user" class="w-5 h-5"></i>
            Profil Saya
        </a>
        <a href="{{ route('settings.show') }}" 
           class="{{ $navBaseClass }} {{ $routeName === 'settings.show' ? $navActiveClass : $navInactiveClass }}">
            <i data-lucide="settings" class="w-5 h-5"></i>
            Pengaturan
        </a>
    </nav>

    <!-- Sidebar Bottom Quote Banner -->
    <div class="p-3 m-3 rounded-xl bg-slate-800/80 border border-slate-700/60 text-center">
        <p class="text-[11px] italic text-slate-400 leading-snug">
            "Barang yang hilang bisa kembali, karena ada yang peduli."
        </p>
    </div>

    <!-- Logout Form -->
    <div class="p-3 border-t border-slate-800/80">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-600 hover:text-white font-semibold text-sm transition-all duration-200 active:scale-95 border border-rose-500/20 shadow-sm cursor-pointer">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Keluar (Logout)
            </button>
        </form>
    </div>
</aside>
