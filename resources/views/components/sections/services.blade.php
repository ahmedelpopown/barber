@props(['services'])

<section id="services" class="w-full bg-[#f8f3ee] px-4 py-16 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-2 sm:px-6">
        <header class="text-center" data-reveal>

            <h2 class="heading-font text-3xl text-[#3d251d] sm:text-4xl md:text-5xl">
                خدماتنا المخصّصة للرجال
            </h2>
            <p class="mt-3 text-sm leading-7 text-[#9a7664]">
                اختر الخدمة المناسبة لك واستمتع بتجربة عناية متكاملة.
            </p>
        </header>

        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:mt-14 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($services as $service)
            @php
            $startingSubService = $service->subServices
            ->sortBy(fn ($subService) => (float) $subService->price)
            ->first();

            $serviceImagePath = 'images/barber-house/services/service-' . $loop->iteration . '.jpg';
            $hasServiceImage = file_exists(public_path($serviceImagePath));
            @endphp

            <article class="flex flex-col gap-4" data-reveal style="--reveal-delay: {{ min($loop->index * 90, 360) }}ms">
                <div class="service-card">
                    <div class="service-card__art bg-gradient-to-br from-[#ead9cf] via-[#9e6a50] to-[#261914] text-white">
                        @if ($hasServiceImage)
                        <img
                            src="{{ asset($serviceImagePath) }}"
                            alt="{{ $service->name }}"
                            loading="lazy"
                            decoding="async">
                        @else
                        <i class="fas fa-cut text-5xl" aria-hidden="true"></i>
                        @endif
                    </div>

                    <div class="overlay service-card__overlay">
                        <span class="service-card__eyebrow">BARBER HOUSE SERVICE {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $service->name }}</h3>
                        @if ($startingSubService)
                        <span class="service-card__price">يبدأ من {{ number_format((float) $startingSubService->price, 2) }} ج.م</span>
                        @else
                        <span class="service-card__price">تفاصيل الأسعار قريبًا</span>
                        @endif
                    </div>
                </div>
                @if ($service->subServices->isNotEmpty())
                <button
                    type="button"
                    onclick="window.Livewire.dispatch('select-service-package', { serviceId: {{ (int) $service->id }}  })"
                    class="group inline-flex w-full items-center justify-center gap-3
               rounded-xl bg-[#5b3025] px-5 py-3.5
               text-sm font-bold text-white
               shadow-md shadow-[#5b3025]/10
               transition-all duration-300
               hover:-translate-y-1 hover:bg-[#734034]
               hover:shadow-lg">
                    <i class="fas fa-calendar-check transition-transform duration-300 group-hover:scale-110"></i>

                    <span>احجز الباقة</span>

                    <i class="fas fa-arrow-left text-xs transition-transform duration-300 group-hover:-translate-x-1"></i>
                </button>
                @endif

                @if ($service->subServices->isNotEmpty())
                <p class="text-center text-sm leading-6 text-[#806b5f]">
                    {{ $service->subServices->count() }} خدمة فرعية متاحة.
                </p>
                @endif
            </article>
            @empty
            <div class="col-span-full rounded-2xl border border-dashed border-[#d7b6a5] bg-[#fcf8f5] px-6 py-12 text-center" data-reveal>
                <i class="fas fa-scissors text-3xl text-[#9b6954]" aria-hidden="true"></i>
                <h3 class="mt-4 text-xl font-bold text-[#5b3025]">الخدمات لم تُضف بعد</h3>
                <p class="mt-2 text-sm text-gray-600">عند إضافة الخدمات من لوحة التحكم ستظهر هنا تلقائيًا.</p>
            </div>
            @endforelse

            <a href="#booking" class="appointment-card" data-reveal style="--reveal-delay: 180ms">
                <span>احجز<br>الآن</span>
                <i class="fas fa-arrow-left mt-3 text-lg" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>