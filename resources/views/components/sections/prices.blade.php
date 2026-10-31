@props(['services'])

<section id="prices" class="w-full bg-[#fffdfb] py-16 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <header class="text-center" data-reveal>
            <p class="mb-2 text-xs font-semibold uppercase tracking-[4px] text-[#a66d4e]">OUR PRICES</p>
            <h2 class="heading-font text-3xl text-[#3d251d] sm:text-4xl md:text-5xl">قائمة الخدمات والأسعار</h2>
            <p class="mt-3 text-sm leading-7 text-[#9a7664]">
                الأسعار بالجنيه المصري وتُحدّث تلقائيًا من لوحة التحكم.
            </p>
        </header>

        <div class="mx-auto mt-10 grid max-w-5xl grid-cols-1 gap-6 md:mt-14 md:grid-cols-2">
            @forelse ($services as $service)
                @php
                    $serviceImagePath = 'images/barber-house/services/service-' . str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) . '.webp';
                    $hasServiceImage = file_exists(public_path($serviceImagePath));
                @endphp

                <article
                    class="price-card"
                    data-reveal
                    style="--reveal-delay: {{ min($loop->index * 90, 360) }}ms"
                >
                    <div class="price-card__cover">
                        @if ($hasServiceImage)
                            <img
                                src="{{ asset($serviceImagePath) }}"
                                alt="{{ $service->name }}"
                                loading="lazy"
                                decoding="async"
                            >
                        @else
                            <div class="price-card__placeholder" aria-hidden="true">
                                <i class="fas fa-cut"></i>
                            </div>
                        @endif
                        <div class="price-card__cover-overlay">
                            <span>BARBER HOUSE</span>
                            <h3>{{ $service->name }}</h3>
                        </div>
                    </div>

                    <div class="divide-y divide-[#f0e7e2] px-5 sm:px-6">
                        @forelse ($service->subServices as $subService)
                            <div class="flex items-start justify-between gap-4 py-4">
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-[#4b2a22]">{{ $subService->name }}</h4>
                                    <p class="mt-1 text-sm text-gray-500">مدة الخدمة: {{ $subService->duration }} دقيقة</p>
                                </div>
                                <div class="shrink-0 text-left font-bold text-[#8f543f]">
                                    {{ number_format((float) $subService->price, 2) }}
                                    <span class="text-xs font-medium">ج.م</span>
                                </div>
                            </div>
                        @empty
                            <p class="py-5 text-sm text-gray-500">لم تُضف خدمات فرعية لهذه المجموعة بعد.</p>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-[#d7b6a5] px-6 py-12 text-center text-gray-600" data-reveal>
                    لا توجد أسعار لعرضها الآن. أضف الخدمات وأسعارها من لوحة التحكم.
                </div>
            @endforelse
        </div>
    </div>
</section>
