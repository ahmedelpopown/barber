@props([
    'variant' => 'neutral',
])

@php
    $variants = [
        'success' => 'bg-green-100 text-green-800 ring-green-600/20',
        'danger' => 'bg-red-100 text-red-800 ring-red-600/20',
        'warning' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
        'info' => 'bg-blue-100 text-blue-800 ring-blue-600/20',
        'neutral' => 'bg-gray-100 text-gray-700 ring-gray-500/20',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset',
    $variants[$variant] ?? $variants['neutral'],
]) }}>
    {{ $slot }}
</span>
