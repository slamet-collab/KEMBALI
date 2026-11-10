@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
        <i data-lucide="bell" class="w-6 h-6 text-rose-500"></i>
        Notifikasi
    </h1>

    <div class="space-y-3">
        @forelse($notifications as $notif)
            <div class="bg-white rounded-2xl border {{ $notif->is_read ? 'border-slate-200/80' : 'border-blue-200 bg-blue-50/30' }} shadow-sm p-5 flex items-start justify-between gap-4 transition-all">
                <div class="flex items-start gap-4">
                    @php
                        $iconMap = ['success' => ['check-circle-2', 'text-emerald-500'], 'info' => ['info', 'text-blue-500'], 'warning' => ['alert-triangle', 'text-amber-500'], 'error' => ['x-circle', 'text-rose-500']];
                        $ic = $iconMap[$notif->type] ?? ['bell', 'text-slate-400'];
                    @endphp
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="{{ $ic[0] }}" class="w-5 h-5 {{ $ic[1] }}"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm {{ $notif->is_read ? '' : 'text-blue-900' }}">{{ $notif->title }}</h4>
                        <p class="text-xs text-slate-600 mt-0.5">{{ $notif->message }}</p>
                        <span class="text-[11px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @if(!$notif->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                        @csrf @method('PUT')
                        <button type="submit" class="text-xs font-bold text-blue-600 hover:underline whitespace-nowrap">Tandai Dibaca</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="text-center py-16">
                <i data-lucide="bell-off" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-sm font-semibold text-slate-500">Belum ada notifikasi.</p>
            </div>
        @endforelse

        @if($notifications->hasPages())
            <div class="pt-4">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>
@endsection
