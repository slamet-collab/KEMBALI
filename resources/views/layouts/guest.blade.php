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
<body class="font-sans bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">

    <!-- Flash Messages Toast / Alert -->
    @if(session('success') || session('error') || session('info') || session('warning'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="fixed top-5 right-5 z-50 max-w-md w-full shadow-2xl rounded-2xl p-4 flex items-start gap-3 transition-all transform duration-300
                    {{ session('success') ? 'bg-emerald-600 text-white' : '' }}
                    {{ session('error') ? 'bg-rose-600 text-white' : '' }}
                    {{ session('warning') ? 'bg-amber-500 text-white' : '' }}
                    {{ session('info') ? 'bg-blue-600 text-white' : '' }}">
            <i data-lucide="info" class="w-6 h-6 flex-shrink-0 mt-0.5"></i>
            <div class="flex-1 text-sm font-medium">
                {{ session('success') ?? session('error') ?? session('warning') ?? session('info') }}
            </div>
            <button @click="show = false" class="text-white/80 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    <!-- Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
