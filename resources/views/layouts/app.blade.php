<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KEMBALI — Campus Lost & Found') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased">

    <!-- Sidebar Component -->
    @include('components.sidebar')

    <!-- Main Content Area Wrapper -->
    <div class="ml-64 min-h-screen flex flex-col">

        <!-- Top Header / Navbar -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 px-6 py-3.5 flex items-center justify-between shadow-sm">
            <!-- Search bar -->
            <div class="relative w-96">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" 
                       placeholder="Cari barang, lokasi, atau kategori..." 
                       class="w-full pl-10 pr-4 py-2 text-sm bg-slate-100/80 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            </div>

            <!-- Header Right Menu -->
            <div class="flex items-center gap-4">
                <!-- Notifications button with badge -->
                <a href="{{ route('notifications.index') }}" 
                   class="relative w-10 h-10 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition-colors">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                </a>

                <div class="h-6 w-px bg-slate-200"></div>

                <!-- User Quick Info -->
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <span class="block text-sm font-bold text-slate-900 leading-tight">{{ Auth::user()->name }}</span>
                        <span class="block text-xs font-medium text-slate-500">{{ Auth::user()->identity_no ?? Auth::user()->role?->name }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-bold text-sm flex items-center justify-center shadow-md shadow-blue-500/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Toast / Alert Banners -->
        @if(session('success') || session('error') || session('info') || session('warning'))
            <div class="px-8 pt-6">
                <div x-data="{ show: true }" x-show="show" 
                     class="rounded-2xl p-4 flex items-center justify-between shadow-sm border
                            {{ session('success') ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : '' }}
                            {{ session('error') ? 'bg-rose-50 text-rose-800 border-rose-200' : '' }}
                            {{ session('warning') ? 'bg-amber-50 text-amber-800 border-amber-200' : '' }}
                            {{ session('info') ? 'bg-blue-50 text-blue-800 border-blue-200' : '' }}">
                    <div class="flex items-center gap-3 text-sm font-semibold">
                        <i data-lucide="info" class="w-5 h-5"></i>
                        <span>{{ session('success') ?? session('error') ?? session('warning') ?? session('info') }}</span>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Main Page Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-8 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} <strong>KEMBALI — Campus Lost & Found</strong>. Universitas Pancasakti Tegal. All Rights Reserved.
        </footer>
    </div>

    <script>
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    </script>
</body>
</html>
