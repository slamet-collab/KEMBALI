@props(['status'])

@php
    $config = match(strtoupper($status)) {
        // Lost & Found Statuses
        'DRAFT' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-300', 'label' => 'Draft'],
        'MENUNGGU_VERIFIKASI' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Menunggu Verifikasi'],
        'DIPUBLIKASIKAN' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'label' => 'Dipublikasikan'],
        'DITEMUKAN' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Ditemukan'],
        'DITAMPUNG' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'label' => 'Ditampung di Pos'],
        'MENUNGGU_KLAIM' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'Menunggu Klaim'],
        'KLAIM_DIPROSES' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'label' => 'Klaim Diproses'],
        'SIAP_DIAMBIL' => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'border-teal-200', 'label' => 'Siap Diambil'],
        'DIKEMBALIKAN' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'border' => 'border-emerald-300', 'label' => 'Telah Dikembalikan'],
        'DITOLAK' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Ditolak'],
        
        // Claim Specific Statuses
        'DIAJUKAN' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'Diajukan'],
        'DIPERIKSA' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Dalam Pemeriksaan'],
        'DISETUJUI' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Disetujui'],
        'SELESAI' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'border' => 'border-emerald-300', 'label' => 'Selesai'],

        default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => $status],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {$config['bg']} {$config['text']} {$config['border']} shadow-2xs"]) }}>
    <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current opacity-75"></span>
    {{ $config['label'] }}
</span>
