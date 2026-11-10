@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <a href="{{ route('found-items.show', $item->id) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-blue-600">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>

    <div class="flex items-center gap-3 pb-2">
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
            <i data-lucide="handshake" class="w-5 h-5"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Ajukan Klaim Kepemilikan</h1>
            <p class="text-xs text-slate-500">Buktikan bahwa barang <strong>{{ $item->title }}</strong> adalah milik Anda.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('claims.store') }}" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf
        <input type="hidden" name="found_item_id" value="{{ $item->id }}">

        <!-- Item Preview -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-4">
            <img src="{{ $item->photos[0] ?? '' }}" alt="" class="w-16 h-16 rounded-xl object-cover border border-slate-200">
            <div>
                <span class="font-bold text-slate-900 text-sm block">{{ $item->title }}</span>
                <span class="text-xs text-slate-500">{{ $item->item_code }} &bull; {{ $item->category?->name }} &bull; {{ $item->found_location }}</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Bukti Kepemilikan <span class="text-rose-500">*</span>
            </label>
            <textarea name="ownership_proof" rows="4" required placeholder="Jelaskan bukti kepemilikan Anda: nota pembelian, serial number, foto sebelumnya, dll..."
                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all resize-none">{{ old('ownership_proof') }}</textarea>
            @error('ownership_proof') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Ciri Khusus yang Hanya Pemilik Tahu <span class="text-rose-500">*</span>
            </label>
            <textarea name="special_characteristics" rows="3" required placeholder="Sebutkan ciri khusus: goresan, stiker, inisial, dll..."
                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all resize-none">{{ old('special_characteristics') }}</textarea>
            @error('special_characteristics') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('found-items.show', $item->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm">Batal</a>
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i>
                Kirim Klaim
            </button>
        </div>
    </form>
</div>
@endsection
