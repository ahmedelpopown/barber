@props([
    'title',
    'description' => null,
])

<header {{ $attributes->class(['flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div class="min-w-0 flex-1">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            {{ $title }}
        </h1>

        @if (filled($description))
            <p class="mt-2 text-sm leading-6 text-gray-600">
                {{ $description }}
            </p>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</header>
