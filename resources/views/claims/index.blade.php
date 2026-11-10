@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
        <i data-lucide="handshake" class="w-6 h-6 text-indigo-500"></i>
        Klaim Saya
    </h1>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode Klaim</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang Diklaim</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($claims as $claim)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg">{{ $claim->claim_code }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-slate-900 text-sm block">{{ $claim->foundItem?->title }}</span>
                                <span class="text-xs text-slate-400">{{ $claim->foundItem?->category?->name }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">{{ $claim->created_at->format('d M Y H:i') }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :status="$claim->status" /></td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('claims.show', $claim->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                    Detail <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <i data-lucide="clipboard-list" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Belum ada klaim yang diajukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($claims->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $claims->links() }}</div>
        @endif
    </div>
</div>
@endsection
