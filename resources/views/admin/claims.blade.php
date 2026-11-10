@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="handshake" class="w-6 h-6 text-indigo-600"></i>
                Kelola Klaim Kepemilikan Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Review bukti klaim dari civitas akademika, ubah status ke Diperiksa, Disetujui, atau Siap Diambil di loker.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode Klaim</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang Diklaim</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Pemohon Klaim</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Bukti & Ciri Khusus</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($claims as $claim)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                                    {{ $claim->claim_code }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900 block">{{ $claim->foundItem?->title ?? 'Barang' }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $claim->foundItem?->item_code }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs">
                                <span class="font-bold text-slate-800 block">{{ $claim->user?->name ?? 'User' }}</span>
                                <span class="text-slate-500">ID: {{ $claim->user?->identity_no ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600 max-w-xs">
                                <p class="truncate font-medium text-slate-800">Ciri: {{ $claim->special_characteristics }}</p>
                                <p class="truncate text-slate-400 mt-0.5">Bukti: {{ $claim->ownership_proof }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <x-status-badge :status="$claim->status" />
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <form method="POST" action="{{ route('admin.claims.update-status', $claim->id) }}" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="text-xs rounded-xl border border-slate-200 px-2.5 py-1.5 bg-slate-50 font-semibold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                        <option value="DIAJUKAN" {{ $claim->status == 'DIAJUKAN' ? 'selected' : '' }}>Diajukan</option>
                                        <option value="DIPERIKSA" {{ $claim->status == 'DIPERIKSA' ? 'selected' : '' }}>Diperiksa</option>
                                        <option value="DISETUJUI" {{ $claim->status == 'DISETUJUI' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="SIAP_DIAMBIL" {{ $claim->status == 'SIAP_DIAMBIL' ? 'selected' : '' }}>Siap Diambil</option>
                                        <option value="DITOLAK" {{ $claim->status == 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                        Ubah
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Tidak ada pengajuan klaim aktif.</p>
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
