@extends('layouts.guest')

@section('content')

<div class="min-h-screen flex items-stretch bg-slate-100">

    <!-- Left Blue Branding Panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 p-12 text-white flex-col justify-between relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&auto=format&fit=crop')] opacity-15 bg-cover bg-center"></div>
        
        <!-- Header Branding -->
        <div class="relative z-10">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white border border-white/20">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="font-extrabold text-xl tracking-tight block">KEMBALI</span>
                    <span class="text-[10px] text-blue-200 tracking-wider uppercase font-bold block">Campus Lost & Found</span>
                </div>
            </a>
        </div>

        <!-- Middle Headline -->
        <div class="relative z-10 space-y-4 max-w-lg">
            <h1 class="text-4xl font-extrabold tracking-tight leading-tight">
                Selamat Datang Kembali!
            </h1>
            <p class="text-blue-100 text-base leading-relaxed">
                Masuk untuk memantau laporan kehilangan, mengecek kecocokan barang temuan, atau mengonfirmasi klaim barang Anda.
            </p>
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 italic text-sm text-blue-50">
                "Kejujuran dan kepedulian Anda membawa barang kembali ke pemiliknya."
            </div>
        </div>

        <!-- Footer Info -->
        <div class="relative z-10 text-xs text-blue-200">
            &copy; {{ date('Y') }} KEMBALI Universitas Pancasakti Tegal.
        </div>
    </div>

    <!-- Right Login Form Panel -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200/80 space-y-6">
            
            <div class="space-y-1">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <i data-lucide="log-in" class="w-6 h-6 text-blue-600"></i>
                    Masuk ke Akun
                </h2>
                <p class="text-xs text-slate-500">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Daftar di sini</a>
                </p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.process') }}" class="space-y-4">
                @csrf

                <!-- NIM / NIP / Email Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        NIM / NIP / EMAIL KAMPUS <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="user" class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" 
                               name="login" 
                               value="{{ old('login') }}" 
                               required 
                               placeholder="Masukkan NIM, NIP, atau email kampus" 
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all">
                    </div>
                    @error('login')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        PASSWORD <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <i data-lucide="lock" class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input :type="show ? 'text' : 'password'" 
                               name="password" 
                               required 
                               placeholder="Masukkan password Anda" 
                               class="w-full pl-11 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all">
                        <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i data-lucide="eye" x-show="!show" class="w-5 h-5"></i>
                            <i data-lucide="eye-off" x-show="show" class="w-5 h-5" x-cloak></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                        <span class="text-xs font-medium text-slate-600">Ingat saya di perangkat ini</span>
                    </label>
                    <a href="#" class="text-xs font-bold text-blue-600 hover:underline">Lupa Password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="arrow-right-circle" class="w-5 h-5"></i>
                    Masuk Sekarang
                </button>

                <!-- Divider -->
                <div class="relative my-4">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                    <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-slate-400 font-bold">Atau</span></div>
                </div>

                <!-- Google OAuth Button -->
                <button type="button" class="w-full py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl border border-slate-200 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                    Masuk dengan Google
                </button>
            </form>

            <div class="text-center pt-2">
                <a href="{{ route('landing') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    &larr; Kembali ke Beranda Utama
                </a>
            </div>

        </div>
    </div>
</div>

@endsection
