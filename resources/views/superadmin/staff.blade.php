@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="shield" class="w-6 h-6 text-amber-500"></i>
                Kelola Staf Barang / Satpam
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar pengguna dengan hak akses Staf Barang (penerimaan fisik barang, loker penyimpanan, dan serah terima).</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Nama & NIP</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">No. WhatsApp</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status Akses</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Terdaftar Sejak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($staff as $member)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $member->name }}</span>
                                        <span class="text-xs text-slate-400 font-mono">{{ $member->identity_no ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">{{ $member->email }}</td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">{{ $member->phone ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                    Staf Aktif
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">{{ $member->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada akun Staf Barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($staff->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $staff->links() }}</div>
        @endif
    </div>
</div>
@endsection
