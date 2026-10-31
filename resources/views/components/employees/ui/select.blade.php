@props([
    'label',
    'name',
    'error' => null,
])

@php
    $inputId = str_replace('.', '-', $name);
@endphp

<div class="space-y-1.5">
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    <select
        id="{{ $inputId }}"
        name="{{ $name }}"
        @if ($error)
            aria-invalid="true"
            aria-describedby="{{ $inputId }}-error"
        @endif
        {{ $attributes->class([
            'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5',
            'text-sm text-gray-900',
            'focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20',
            $error ? 'border-red-500' : '',
        ]) }}
    >
        {{ $slot }}
    </select>

    @if ($error)
        <p id="{{ $inputId }}-error" class="text-sm text-red-600">
            {{ $error }}
        </p>
    @endif
</div>
