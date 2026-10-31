@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->class(['overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm']) }}>
    @if (filled($title) || filled($description))
        <header class="border-b border-gray-200 px-4 py-4 sm:px-6">
            @if (filled($title))
                <h2 class="text-base font-semibold text-gray-900">
                    {{ $title }}
                </h2>
            @endif

            @if (filled($description))
                <p class="mt-1 text-sm text-gray-600">
                    {{ $description }}
                </p>
            @endif
        </header>
    @endif

    <div class="p-4 sm:p-6">
        {{ $slot }}
    </div>
</section>
