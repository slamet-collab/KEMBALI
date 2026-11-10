@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
        <i data-lucide="user" class="w-6 h-6 text-blue-600"></i>
        Profil Saya
    </h1>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-blue-600 text-white font-bold text-2xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">{{ $user->name }}</h2>
                <span class="text-sm text-slate-500">{{ $user->email }}</span>
                <div class="mt-1">
                    <span class="px-3 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ $user->role?->name }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">NIM / NIP</span>
                <span class="text-sm font-semibold text-slate-900">{{ $user->identity_no ?? '-' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">No. Telepon</span>
                <span class="text-sm font-semibold text-slate-900">{{ $user->phone ?? '-' }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Akun</span>
                <span class="text-sm font-semibold {{ $user->status === 'active' ? 'text-emerald-600' : 'text-rose-600' }}">{{ ucfirst($user->status) }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Terdaftar Sejak</span>
                <span class="text-sm font-semibold text-slate-900">{{ $user->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
