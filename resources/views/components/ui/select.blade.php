@props([
    'label',
    'name',
    'error' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
])

@php
    $inputId = str_replace('.', '-', $name);
    $errorMessage = filled($error) ? $error : $errors->first($name);
    $hasError = filled($errorMessage);
@endphp

<div class="space-y-1.5">
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
        @if ($required)
            <span class="text-red-600" aria-hidden="true">*</span>
        @endif
    </label>

    <select
        id="{{ $inputId }}"
        name="{{ $name }}"
        @required($required)
        @disabled($disabled)
        @if ($hasError)
            aria-invalid="true"
            aria-describedby="{{ $inputId }}-error"
        @endif
        {{ $attributes->class([
            'block w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-gray-900',
            'focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-gray-100',
            $hasError
                ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500/20',
        ]) }}
    >
        @if ($placeholder !== null)
            <option value="" @disabled($required)>{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>

    @if ($hasError)
        <p id="{{ $inputId }}-error" class="text-sm text-red-600" role="alert">
            {{ $errorMessage }}
        </p>
    @endif
</div>
