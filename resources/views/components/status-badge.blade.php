@props(['status' => ''])

@php
    $colors = [
        'terverifikasi' => 'bg-emerald-100 text-emerald-800',
        'diberikan' => 'bg-emerald-100 text-emerald-800',
        'aktif' => 'bg-indigo-100 text-indigo-800',
        'link_dibuka' => 'bg-amber-100 text-amber-800',
        'ditutup' => 'bg-gray-100 text-gray-700',
        'ditolak' => 'bg-rose-100 text-rose-800',
        'gagal' => 'bg-rose-100 text-rose-800',
        '' => 'bg-gray-100 text-gray-700',
    ];
    $class = $colors[$status] ?? $colors[''];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' . $class]) }}>
    {{ $slot }}
</span>
