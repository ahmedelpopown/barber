@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'error' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'min' => null,
    'max' => null,
    'step' => null,
])

@php
    $inputId = str_replace('.', '-', $name);
    $errorMessage = filled($error) ? $error : $errors->first($name);
    $hasError = filled($errorMessage);

    $inputTypes = ['text', 'password', 'number', 'email', 'tel', 'url', 'search', 'date', 'time', 'range'];
    $inputType = in_array($type, $inputTypes, true) ? $type : 'text';

    $controlClasses = [
        'block w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-gray-900 placeholder:text-gray-400',
        'focus:outline-none focus:ring-2',
        'disabled:cursor-not-allowed disabled:bg-gray-100 disabled:opacity-70',
        $hasError
            ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
            : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500/20',
    ];
@endphp

<div class="space-y-1.5">
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
        @if ($required)
            <span class="text-red-600" aria-hidden="true">*</span>
        @endif
    </label>

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $inputType }}"
        @if ($inputType !== 'password' && $value !== null)
            value="{{ $value }}"
        @endif
        @if ($placeholder !== null)
            placeholder="{{ $placeholder }}"
        @endif
        @if ($min !== null)
            min="{{ $min }}"
        @endif
        @if ($max !== null)
            max="{{ $max }}"
        @endif
        @if ($step !== null)
            step="{{ $step }}"
        @endif
        @required($required)
        @disabled($disabled)
        @if ($hasError)
            aria-invalid="true"
            aria-describedby="{{ $inputId }}-error"
        @endif
        {{ $attributes->class($controlClasses) }}
    >

    @if ($hasError)
        <p id="{{ $inputId }}-error" class="text-sm text-red-600" role="alert">
            {{ $errorMessage }}
        </p>
    @endif
</div>
