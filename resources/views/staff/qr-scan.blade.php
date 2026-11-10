@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <i data-lucide="qr-code" class="w-6 h-6 text-indigo-600"></i>
            Pemindaian QR & Pencarian Cepat
        </h1>
        <p class="text-xs text-slate-500 mt-1">Pindai kode QR pada bukti klaim atau masukkan kode barang/loker secara langsung untuk verifikasi cepat.</p>
    </div>

    <!-- Scanner Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
        <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
            <div class="w-48 h-48 rounded-2xl bg-slate-900 flex flex-col items-center justify-center text-white relative overflow-hidden shadow-inner">
                <i data-lucide="scan" class="w-20 h-20 text-blue-400 animate-pulse"></i>
                <div class="absolute inset-x-0 h-0.5 bg-blue-500 shadow-[0_0_12px_#3b82f6] animate-bounce top-1/2"></div>
            </div>
            <p class="text-sm font-semibold text-slate-700 mt-4">Arahkan kamera ke QR Code bukti klaim atau label loker</p>
            <p class="text-xs text-slate-400 mt-1">Kamera scanner aktif otomatis bila didukung browser perangkat.</p>
        </div>

        <!-- Manual Lookup Form -->
        <div class="border-t border-slate-100 pt-6">
            <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <i data-lucide="keyboard" class="w-4 h-4 text-slate-500"></i>
                Atau Cari Berdasarkan Kode Manual
            </h3>
            <form onsubmit="handleManualSearch(event)" class="flex gap-3">
                <input type="text" id="manualCode" required placeholder="Contoh: CLM-2026-0001 atau FND-2026-0001" 
                       class="flex-1 text-sm rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Cari Data
                </button>
            </form>
            <div id="searchResult" class="hidden mt-4 p-4 rounded-xl border"></div>
        </div>
    </div>
</div>

<script>
    function handleManualSearch(e) {
        e.preventDefault();
        const code = document.getElementById('manualCode').value.trim();
        const resultBox = document.getElementById('searchResult');
        
        if (!code) return;
        
        resultBox.classList.remove('hidden');
        resultBox.className = "mt-4 p-4 rounded-xl border bg-blue-50 border-blue-100 text-blue-900 text-sm";
        resultBox.innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-bold">Mencari Kode: <span class="font-mono">${code}</span></p>
                    <p class="text-xs text-blue-700 mt-1">Silakan pilih menu tujuan berdasarkan kode di atas:</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('staff.handover') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors">
                        Buka Serah Terima
                    </a>
                    <a href="{{ route('staff.storage') }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-colors">
                        Buka Loker
                    </a>
                </div>
            </div>
        `;
    }
</script>
@endsection
