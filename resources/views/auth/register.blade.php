@extends('layouts.guest')

@section('content')

<div class="min-h-screen flex items-stretch bg-slate-100">

    <!-- Left Blue Branding Panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 p-12 text-white flex-col justify-between relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1200&auto=format&fit=crop')] opacity-15 bg-cover bg-center"></div>
        
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
                Buat Akun KEMBALI
            </h1>
            <p class="text-blue-100 text-base leading-relaxed">
                Bergabunglah dengan KEMBALI untuk mendapatkan kemudahan dalam melaporkan, mencari, dan menemukan barang di lingkungan kampus.
            </p>
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 italic text-sm text-blue-50">
                "Jadi bagian dari solusi kampus yang lebih baik!"
            </div>
        </div>

        <!-- Footer Info -->
        <div class="relative z-10 text-xs text-blue-200">
            &copy; {{ date('Y') }} KEMBALI Universitas Pancasakti Tegal.
        </div>
    </div>

    <!-- Right Register Form Panel -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-lg bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200/80 space-y-5">
            
            <div class="space-y-1">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-6 h-6 text-blue-600"></i>
                    Daftar Akun Baru
                </h2>
                <p class="text-xs text-slate-500">
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Masuk di sini</a>
                </p>
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register.process') }}" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        NAMA LENGKAP <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           placeholder="Masukkan nama lengkap Anda" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all">
                    @error('name')<p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- NIM / NIP -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        NIM / NIP / ID KAMPUS <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="identity_no" 
                           value="{{ old('identity_no') }}" 
                           inputmode="numeric"
                           pattern="[0-9]+"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           required 
                           placeholder="Masukkan NIM / NIP berupa angka saja" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all">
                    @error('identity_no')<p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Email Kampus -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        EMAIL KAMPUS <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           placeholder="contoh@upstegal.ac.id" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all">
                    @error('email')<p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Password Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            PASSWORD <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               required 
                               placeholder="Min. 8 karakter" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all">
                        @error('password')<p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            KONFIRMASI PASSWORD <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               required 
                               placeholder="Ulangi password" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Terms checkbox -->
                <div class="pt-1">
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" name="terms" value="1" required class="w-4 h-4 mt-0.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                        <span class="text-xs text-slate-600">Saya menyetujui <a href="#" class="text-blue-600 font-bold hover:underline">Syarat & Ketentuan</a> serta <a href="#" class="text-blue-600 font-bold hover:underline">Kebijakan Privasi</a> KEMBALI.</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    Daftar Akun Sekarang
                </button>
            </form>

            <div class="text-center pt-1">
                <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Sudah punya akun? <span class="text-blue-600 font-bold">Masuk di sini</span>
                </a>
            </div>

        </div>
    </div>
</div>

@endsection
