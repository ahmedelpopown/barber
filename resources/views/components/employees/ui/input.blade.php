@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'error' => null,
    'placeholder' => null,
    'options' => [],
    'searchable' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'disabled' => false,
    'required' => false,
])

@php
    $inputId = preg_replace('/[^A-Za-z0-9\-_]/', '-', $name);

    $inputTypes = [
        'text',
        'password',
        'number',
        'email',
        'tel',
        'url',
        'search',
        'date',
        'time',
        'range',
    ];

    $inputType = in_array($type, $inputTypes, true)
        ? $type
        : 'text';

    $currentValue = $value ?? old($name, '');

    $errorMessage = filled($error)
        ? $error
        : $errors->first($name);

    $hasError = filled($errorMessage);

    /*
    |--------------------------------------------------------------------------
    | Normalize options
    |--------------------------------------------------------------------------
    | Supports:
    | ['active' => 'يعمل', 'inactive' => 'لا يعمل']
    | ['Ahmed', 'Mohamed']
    | [['value' => 1, 'label' => 'Ahmed']]
    | Eloquent models with id and name attributes.
    */

    $normalizedOptions = collect($options)
        ->map(function ($option, $key) {
            if (is_array($option) || is_object($option)) {
                $optionValue = data_get(
                    $option,
                    'value',
                    data_get($option, 'id', $key)
                );

                $optionLabel = data_get(
                    $option,
                    'label',
                    data_get($option, 'name', $optionValue)
                );
            } else {
                $optionValue = is_int($key)
                    ? $option
                    : $key;

                $optionLabel = $option;
            }

            return [
                'value' => (string) $optionValue,
                'label' => (string) $optionLabel,
            ];
        })
        ->values()
        ->all();

    $selectedOption = collect($normalizedOptions)->first(
        fn ($option) => $option['value'] === (string) $currentValue
    );

    $selectedLabel = $selectedOption['label'] ?? '';

    $controlClasses = [
        'block w-full rounded-lg border bg-white px-3 py-2.5',
        'text-sm text-gray-900 placeholder:text-gray-400',
        'focus:outline-none focus:ring-2',
        'disabled:cursor-not-allowed disabled:bg-gray-100 disabled:opacity-70',
        $hasError
            ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
            : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500/20',
    ];

    /*
    |--------------------------------------------------------------------------
    | Searchable select attributes
    |--------------------------------------------------------------------------
    | Keep wire:model on the hidden input, not on the search text.
    */

    $modelAttributes = $attributes->whereStartsWith('wire:model');

    $controlAttributes = $attributes->whereDoesntStartWith('wire:model');
@endphp

<div class="space-y-1.5">
    <label
        for="{{ $inputId }}"
        class="block text-sm font-medium text-gray-700"
    >
        {{ $label }}
        @if ($required)
            <span class="text-red-600" aria-hidden="true">*</span>
        @endif
    </label>

    {{-- Select --}}
    @if ($type === 'select' && ! $searchable)
        <select
            id="{{ $inputId }}"
            name="{{ $name }}"
            @disabled($disabled)
            @required($required)
            @if ($hasError)
                aria-invalid="true"
                aria-describedby="{{ $inputId }}-error"
            @endif
            {{ $attributes->class($controlClasses) }}
        >
            @if ($placeholder !== null)
                <option
                    value=""
                    @selected((string) $currentValue === '')
                    @disabled($required)
                >
                    {{ $placeholder }}
                </option>
            @endif

            @if (count($normalizedOptions))
                @foreach ($normalizedOptions as $option)
                    <option
                        value="{{ $option['value'] }}"
                        @selected($option['value'] === (string) $currentValue)
                    >
                        {{ $option['label'] }}
                    </option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>

    {{-- Searchable Select --}}
    @elseif ($type === 'select' && $searchable)
        <div
            class="relative"
            x-data="{
                open: false,
                search: @js($selectedLabel),
                selectedValue: @js((string) $currentValue),
                activeIndex: -1,
                options: @js($normalizedOptions),

                filteredOptions() {
                    const query = this.search.toLocaleLowerCase();

                    return this.options.filter(option =>
                        option.label.toLocaleLowerCase().includes(query)
                    );
                },

                select(option) {
                    this.selectedValue = String(option.value);
                    this.search = option.label;
                    this.activeIndex = -1;
                    this.open = false;

                    this.$refs.modelInput.value = this.selectedValue;

                    this.$refs.modelInput.dispatchEvent(
                        new Event('input', { bubbles: true })
                    );

                    this.$refs.searchInput.setCustomValidity('');
                },
            }"
            @click.outside="open = false"
        >
            {{-- Actual value connected to Livewire --}}
            <input
                type="hidden"
                id="{{ $inputId }}-value"
                name="{{ $name }}"
                value="{{ $currentValue }}"
                x-ref="modelInput"
                {{ $modelAttributes }}
            >

            {{-- Search input --}}
            <input
                id="{{ $inputId }}"
                type="text"
                role="combobox"
                aria-autocomplete="list"
                aria-haspopup="listbox"
                aria-controls="{{ $inputId }}-options"
                :aria-expanded="open.toString()"
                :aria-activedescendant="
                    activeIndex >= 0
                        ? '{{ $inputId }}-option-' + activeIndex
                        : null
                "
                x-ref="searchInput"
                x-model="search"
                placeholder="{{ $placeholder ?? 'ابحث واختر...' }}"
                autocomplete="off"
                @focus="open = true"
                @input="
                    open = true;
                    activeIndex = -1;

                    if (selectedValue !== '') {
                        selectedValue = '';
                        $refs.modelInput.value = '';

                        $refs.modelInput.dispatchEvent(
                            new Event('input', { bubbles: true })
                        );
                    }

                    if (@js($required)) {
                        $refs.searchInput.setCustomValidity(
                            'اختر قيمة من القائمة.'
                        );
                    }
                "
                @keydown.arrow-down.prevent="
                    open = true;
                    activeIndex = Math.min(
                        activeIndex + 1,
                        filteredOptions().length - 1
                    );
                "
                @keydown.arrow-up.prevent="
                    activeIndex = Math.max(activeIndex - 1, 0);
                "
                @keydown.enter="
                    if (open && filteredOptions().length) {
                        $event.preventDefault();

                        select(
                            filteredOptions()[
                                activeIndex < 0 ? 0 : activeIndex
                            ]
                        );
                    }
                "
                @keydown.escape="open = false"
                @disabled($disabled)
                @required($required)
                @if ($hasError)
                    aria-invalid="true"
                    aria-describedby="{{ $inputId }}-error"
                @endif
                {{ $controlAttributes
                    ->class($controlClasses)
                    ->merge(['autocomplete' => 'off']) }}
            >

            {{-- Dropdown --}}
            <div
                x-show="open"
                x-transition
                style="display: none;"
                class="absolute z-30 mt-1 max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
            >
                <ul
                    id="{{ $inputId }}-options"
                    role="listbox"
                    class="outline-none"
                >
                    <template
                        x-for="(option, index) in filteredOptions()"
                        :key="String(option.value)"
                    >
                        <li>
                            <button
                                type="button"
                                role="option"
                                :id="'{{ $inputId }}-option-' + index"
                                :aria-selected="
                                    selectedValue === String(option.value)
                                "
                                @mouseenter="activeIndex = index"
                                @click="select(option)"
                                :class="
                                    activeIndex === index
                                        ? 'bg-indigo-50 text-indigo-700'
                                        : 'text-gray-900'
                                "
                                class="flex w-full items-center px-3 py-2.5 text-start text-sm hover:bg-indigo-50"
                            >
                                <span x-text="option.label"></span>
                            </button>
                        </li>
                    </template>
                </ul>

                <p
                    x-show="filteredOptions().length === 0"
                    class="px-3 py-3 text-sm text-gray-500"
                >
                    لا توجد نتائج مطابقة.
                </p>
            </div>
        </div>

    {{-- Range Slider --}}
    @elseif ($type === 'range')
        <div
            x-data="{
                currentValue: @js((string) ($currentValue !== '' ? $currentValue : ($min ?? 0)))
            }"
            class="space-y-2"
        >
            <input
                id="{{ $inputId }}"
                name="{{ $name }}"
                type="range"
                value="{{ $currentValue !== '' ? $currentValue : ($min ?? 0) }}"
                @if ($min !== null) min="{{ $min }}" @endif
                @if ($max !== null) max="{{ $max }}" @endif
                @if ($step !== null) step="{{ $step }}" @endif
                x-model="currentValue"
                @disabled($disabled)
                @required($required)
                @if ($hasError)
                    aria-invalid="true"
                    aria-describedby="{{ $inputId }}-error"
                @endif
                {{ $attributes->class([
                    'block w-full cursor-pointer accent-indigo-600',
                    'disabled:cursor-not-allowed disabled:opacity-70',
                ]) }}
            >

            <div class="flex items-center justify-between text-sm text-gray-500">
                @if ($min !== null)
                    <span>{{ $min }}</span>
                @endif

                <output
                    for="{{ $inputId }}"
                    class="font-semibold text-indigo-700"
                    x-text="currentValue"
                ></output>

                @if ($max !== null)
                    <span>{{ $max }}</span>
                @endif
            </div>
        </div>

    {{-- Text, Password, Number, Email, etc. --}}
    @else
        <input
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="{{ $inputType }}"
            @if ($inputType !== 'password')
                value="{{ $currentValue }}"
            @endif
            @if ($placeholder !== null)
                placeholder="{{ $placeholder }}"
            @endif
            @if (in_array($inputType, ['number', 'range'], true))
                @if ($min !== null) min="{{ $min }}" @endif
                @if ($max !== null) max="{{ $max }}" @endif
                @if ($step !== null) step="{{ $step }}" @endif
            @endif
            @disabled($disabled)
            @required($required)
            @if ($hasError)
                aria-invalid="true"
                aria-describedby="{{ $inputId }}-error"
            @endif
            {{ $attributes->class($controlClasses) }}
        >
    @endif

    {{-- Validation Error --}}
    @if ($hasError)
        <p
            id="{{ $inputId }}-error"
            class="text-sm text-red-600"
            role="alert"
        >
            {{ $errorMessage }}
        </p>
    @endif
</div>