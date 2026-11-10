@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                Kelola Pengguna Sistem
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar pengguna dan pengaturan role akses. Perubahan role dicatat di Activity Log.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Nama & ID</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">No. WhatsApp</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Role Akses</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $user->name }}</span>
                                        <span class="text-xs text-slate-400 font-mono">{{ $user->identity_no ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">{{ $user->email }}</td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">{{ $user->phone ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                @if((int) Auth::id() !== (int) $user->id && ($isSuperAdmin || $user->role?->code !== 'super_admin'))
                                    <form method="POST" action="{{ route($roleUpdateRouteName, $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role_id" aria-label="Role untuk {{ $user->name }}" class="min-w-32 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-xs font-semibold text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ (int) $user->role_id === (int) $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-2.5 py-2 text-xs font-bold text-white transition-colors hover:bg-blue-700">
                                            <i data-lucide="save" class="h-3.5 w-3.5"></i>
                                            Simpan
                                        </button>
                                    </form>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                                        {{ $user->role?->code === 'super_admin' ? 'bg-purple-50 text-purple-700 border border-purple-100' : '' }}
                                        {{ $user->role?->code === 'admin' ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}
                                        {{ $user->role?->code === 'staf_barang' ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                                        {{ in_array($user->role?->code, ['mahasiswa', 'dosen']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}">
                                        {{ $user->role?->name ?? 'Mahasiswa' }}
                                    </span>
                                    @if((int) Auth::id() === (int) $user->id)
                                        <span class="mt-1 block text-[11px] text-slate-400">Role akun aktif tidak dapat diubah.</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
