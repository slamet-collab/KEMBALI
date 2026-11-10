@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="handshake" class="w-6 h-6 text-emerald-600"></i>
                Serah Terima Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar klaim yang telah disetujui Admin dan berstatus "Siap Diambil" oleh pemilik barang.</p>
        </div>
        <a href="{{ route('staff.handover-history') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
            <i data-lucide="history" class="w-4 h-4"></i>
            Riwayat Serah Terima
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Kode Klaim</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Barang</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Penerima (Pemilik)</th>
                        <th class="text-left px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Tanggal Disetujui</th>
                        <th class="text-right px-5 py-3.5 font-bold text-xs text-slate-500 uppercase tracking-wider">Aksi Serah Terima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($claims as $claim)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    {{ $claim->claim_code }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900 block">{{ $claim->foundItem?->title ?? 'Barang' }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $claim->foundItem?->item_code }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-800 block text-xs">{{ $claim->user?->name }}</span>
                                <span class="text-xs text-slate-500">NIM/NIDN: {{ $claim->user?->identity_no ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                {{ $claim->verified_at ? $claim->verified_at->format('d M Y H:i') : $claim->updated_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <button type="button" 
                                        onclick="openHandoverModal({{ $claim->id }}, '{{ $claim->claim_code }}', '{{ addslashes($claim->foundItem?->title ?? 'Barang') }}', '{{ addslashes($claim->user?->name ?? 'Pemilik') }}', '{{ $claim->user?->identity_no ?? '' }}')" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                    <i data-lucide="check-square" class="w-3.5 h-3.5"></i>
                                    Proses Serah Terima
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-sm font-semibold text-slate-500">Tidak ada barang yang sedang menunggu pengambilan.</p>
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

<!-- Modal Serah Terima -->
<div id="handoverModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                <i data-lucide="handshake" class="w-5 h-5 text-emerald-600"></i>
                Konfirmasi Serah Terima Barang
            </h3>
            <button onclick="closeHandoverModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" id="handoverForm" action="{{ route('staff.handover.process') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="claim_id" id="modalClaimId">

            <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-xl space-y-1 text-xs">
                <p class="text-emerald-900 font-bold" id="modalHandoverClaim">Klaim: -</p>
                <p class="text-emerald-700" id="modalHandoverItem">Barang: -</p>
                <p class="text-emerald-700" id="modalHandoverOwner">Penerima: -</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Identitas Penerima (KTM/KTP/NIP)</label>
                <input type="text" name="recipient_identity_no" id="modalRecipientId" required placeholder="Contoh: 12345678" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Penyerahan</label>
                <textarea name="notes" rows="2" placeholder="Barang diserahkan dalam kondisi lengkap dan telah dicek..." class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeHandoverModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors">Selesaikan Serah Terima</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openHandoverModal(id, claimCode, itemTitle, userName, identityNo) {
        document.getElementById('modalClaimId').value = id;
        document.getElementById('modalHandoverClaim').innerText = 'Kode Klaim: ' + claimCode;
        document.getElementById('modalHandoverItem').innerText = 'Barang: ' + itemTitle;
        document.getElementById('modalHandoverOwner').innerText = 'Nama Penerima: ' + userName;
        document.getElementById('modalRecipientId').value = identityNo || '';
        document.getElementById('handoverModal').classList.remove('hidden');
    }
    function closeHandoverModal() {
        document.getElementById('handoverModal').classList.add('hidden');
    }
</script>
@endsection
