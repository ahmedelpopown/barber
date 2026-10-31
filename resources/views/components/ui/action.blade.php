@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
])

@php
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'danger' => 'btn-danger',
      'ghost' => '!bg-gray-100 text-gray-700 hover:!bg-gray-200 focus-visible:ring-indigo-500',
    ];

    $classes = [
        'btn',
        $variants[$variant] ?? 'btn-primary',
        'btn-sm' => $size === 'sm',
        'btn-lg' => $size === 'lg',
        'disabled' => $disabled && $href !== null,
    ];
@endphp

@if ($href !== null)
    <a
        @if (!$disabled) href="{{ $href }}" @endif
        @if ($disabled) aria-disabled="true" tabindex="-1" @endif
        {{ $attributes->class($classes) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        @disabled($disabled)
        {{ $attributes->class($classes) }}
    >
        {{ $slot }}
    </button>
@endif