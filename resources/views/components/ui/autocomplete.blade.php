@props([
    'label',
    'searchModel',
    'search',
    'options' => [],
    'selectMethod',
    'placeholder' => 'ابدأ الكتابة للبحث...',
    'selected' => false,
    'emptyMessage' => 'لا توجد نتائج مطابقة.',
    'minimumCharacters' => 2,
    'id' => null,
])

@php
    $fieldId = $id ?: 'autocomplete-' . preg_replace('/[^A-Za-z0-9\-_]/', '-', $searchModel);
@endphp

<div class="relative space-y-1.5">
    <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    <input
        id="{{ $fieldId }}"
        type="search"
        wire:model.live.debounce.300ms="{{ $searchModel }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        aria-autocomplete="list"
        aria-label="{{ $label }}"
        {{ $attributes->class([
            'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5',
            'text-sm text-gray-900 placeholder:text-gray-400',
            'focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20',
        ]) }}
    >

    @if (mb_strlen(trim((string) $search)) >= $minimumCharacters && count($options))
        <ul class="absolute z-30 mt-1 max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg" role="listbox">
            @foreach ($options as $option)
                @php
                    $optionId = data_get($option, 'id');
                    $optionLabel = data_get($option, 'name', '');
                @endphp

                @if ($optionId !== null)
                    <li wire:key="{{ $fieldId }}-option-{{ $optionId }}">
                        <button
                            type="button"
                            role="option"
                            wire:click="{{ $selectMethod }}({{ (int) $optionId }})"
                            class="flex w-full items-center px-3 py-2.5 text-start text-sm text-gray-800 transition hover:bg-indigo-50 hover:text-indigo-700"
                        >
                            {{ $optionLabel }}
                        </button>
                    </li>
                @endif
            @endforeach
        </ul>
    @elseif (mb_strlen(trim((string) $search)) >= $minimumCharacters && ! $selected)
        <p class="absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-3 text-sm text-gray-500 shadow-lg">
            {{ $emptyMessage }}
        </p>
    @endif
</div>
