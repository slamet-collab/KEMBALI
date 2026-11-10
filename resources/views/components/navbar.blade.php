<nav class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- Logo Branding -->
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/25 group-hover:scale-105 transition-transform">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight text-slate-900 block leading-tight">KEMBALI</span>
                    <span class="text-[11px] text-blue-600 tracking-widest font-bold uppercase block">Campus Lost & Found</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center gap-8 font-semibold text-sm text-slate-600">
                <a href="{{ route('landing') }}" class="text-blue-600 font-bold transition-colors">Beranda</a>
                <a href="#tentang" class="hover:text-blue-600 transition-colors">Tentang</a>
                <a href="#cara-kerja" class="hover:text-blue-600 transition-colors">Cara Kerja</a>
                <a href="{{ route('public.found-items') }}" class="hover:text-blue-600 transition-colors">Barang Ditemukan</a>
                <a href="#faq" class="hover:text-blue-600 transition-colors">FAQ</a>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ route('public.found-items') }}" class="p-2.5 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-slate-100 transition-colors" title="Cari Barang">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </a>

                @auth
                    <a href="{{ route('dashboard.redirect') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/25 transition-all flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold text-sm transition-all">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/25 transition-all">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
