@props([
    'type' => 'info',
    'message' => null,
])

@php
    $styles = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'danger' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
    ];
@endphp

@if (filled($message))
    <div
        role="{{ $type === 'danger' ? 'alert' : 'status' }}"
        aria-live="{{ $type === 'danger' ? 'assertive' : 'polite' }}"
        {{ $attributes->class([
            'rounded-lg border px-4 py-3 text-sm',
            $styles[$type] ?? $styles['info'],
        ]) }}
    >
        {{ $message }}
    </div>
@endif
