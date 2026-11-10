@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
        <i data-lucide="settings" class="w-6 h-6 text-slate-600"></i>
        Pengaturan
    </h1>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="p-8 text-center rounded-2xl bg-slate-50 border border-dashed border-slate-200">
            <i data-lucide="construction" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h3 class="font-bold text-slate-600 text-lg">Halaman Pengaturan</h3>
            <p class="text-sm text-slate-500 mt-1">Fitur pengaturan profil, password, dan notifikasi sedang dalam pengembangan.</p>
        </div>
    </div>
</div>
@endsection
