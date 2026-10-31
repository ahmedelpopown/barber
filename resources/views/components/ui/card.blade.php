@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->class(['overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm']) }}>
    @if ($title || $description || isset($header))
        <header class="flex flex-col gap-3 border-b border-gray-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            @if ($title || $description)
                <div class="min-w-0">
                    @if ($title)
                        <h2 class="text-base font-semibold text-gray-900">{{ $title }}</h2>
                    @endif

                    @if ($description)
                        <p class="mt-1 text-sm text-gray-600">{{ $description }}</p>
                    @endif
                </div>
            @endif

            @isset($header)
                <div class="shrink-0">
                    {{ $header }}
                </div>
            @endisset
        </header>
    @endif

    <div class="p-4 sm:p-6">
        {{ $slot }}
    </div>
</section>
