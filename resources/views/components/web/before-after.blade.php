@props([
    'before',
    'after',
    'beforeLabel' => 'قبل',
    'afterLabel' => 'بعد',
    'alt' => 'مقارنة الصورة قبل وبعد',
])

<div class="before-after" data-before-after style="--before-after-position: 50%;">
    <img
        src="{{ $after }}"
        alt="{{ $alt }} - {{ $afterLabel }}"
        class="before-after__image"
        loading="lazy"
        decoding="async"
    >

    <div class="before-after__before" data-before-after-layer>
        <img
            src="{{ $before }}"
            alt="{{ $alt }} - {{ $beforeLabel }}"
            class="before-after__image"
            loading="lazy"
            decoding="async"
        >
    </div>

    <div class="before-after__divider" aria-hidden="true">
        <span>↔</span>
    </div>

    <span class="before-after__label before-after__label--before">{{ $beforeLabel }}</span>
    <span class="before-after__label before-after__label--after">{{ $afterLabel }}</span>

    <input
        type="range"
        min="0"
        max="100"
        value="50"
        step="1"
        dir="ltr"
        class="before-after__range"
        data-before-after-range
        aria-label="حرّك للمقارنة بين الصورة قبل وبعد"
    >
</div>
