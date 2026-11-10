@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="key" class="w-6 h-6 text-purple-600"></i>
                Manajemen Peran & Hak Akses (RBAC)
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar peran (roles) dan izin (permissions) yang mengatur otoritas setiap pengguna di sistem KEMBALI.</p>
        </div>
        <a href="{{ route('superadmin.users') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-purple-700">
            <i data-lucide="users" class="h-4 w-4"></i>
            Ubah Role Pengguna
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-xl text-xs font-bold uppercase tracking-wider font-mono
                        {{ $role->code === 'super_admin' ? 'bg-purple-50 text-purple-700 border border-purple-100' : '' }}
                        {{ $role->code === 'admin' ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}
                        {{ $role->code === 'staf_barang' ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                        {{ in_array($role->code, ['mahasiswa', 'dosen']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}">
                        {{ $role->code }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500 flex items-center gap-1">
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        {{ $role->users_count }} Pengguna
                    </span>
                </div>

                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">{{ $role->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $role->description ?? 'Hak akses default untuk role ini.' }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <span class="block text-xs font-bold text-slate-700 uppercase mb-2">Permissions:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($role->permissions as $permission)
                            <span class="text-[11px] font-medium bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">
                                {{ $permission->name ?? $permission->code }}
                            </span>
                        @empty
                            <span class="text-xs text-slate-400 italic">Semua akses sistem (Super Administrator)</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
