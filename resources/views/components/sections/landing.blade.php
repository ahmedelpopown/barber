<section aria-label="باربر هاوس" class="relative">
    <div class="swiper heroSwiper">
        <div class="swiper-wrapper">
            @php
                $heroSlides = [
                    [
                        'image' => 'images/barber-house/hero/hero-01.webp',
                        'legacy' => 'images/2.jpg',
                        'alt' => 'حلاقة شعر احترافية في باربر هاوس',
                        'title' => 'حلاقة راقية تعكس ذوقك',
                        'description' => 'خبرة تُحسّن ملامحك وتمنحك ثقةً لا تُقاوم في كل لحظة.',
                    ],
                    [
                        'image' => 'images/barber-house/hero/hero-02.webp',
                        'legacy' => 'images/1.jpg',
                        'alt' => 'خدمات الحلاقة والعناية في باربر هاوس',
                        'title' => 'احجز موعدك في ثوانٍ',
                        'description' => 'تجربة حجز سلسة، مريحة، ومصممة لتناسب أسلوب حياتك.',
                    ],
                ];
            @endphp

            @foreach ($heroSlides as $slide)
                <div class="swiper-slide">
                    @if (file_exists(public_path($slide['image'])))
                        <img
                            src="{{ asset($slide['image']) }}"
                            alt="{{ $slide['alt'] }}"
                            class="hero-image"
                            @if ($loop->first) fetchpriority="high" loading="eager" @else loading="lazy" @endif
                            decoding="async"
                        >
                    @elseif (file_exists(public_path($slide['legacy'])))
                        <img
                            src="{{ asset($slide['legacy']) }}"
                            alt="{{ $slide['alt'] }}"
                            class="hero-image"
                            @if ($loop->first) fetchpriority="high" loading="eager" @else loading="lazy" @endif
                            decoding="async"
                        >
                    @else
                        <div class="hero-image absolute inset-0 bg-gradient-to-br from-[#1e1512] via-[#60382b] to-[#b8876b]"></div>
                    @endif

                    <div class="hero-overlay" aria-hidden="true"></div>

                    <div class="hero-content">
                        <h1 class="heading-font">{{ $slide['title'] }}</h1>
                        <p>{{ $slide['description'] }}</p>
                        <a href="#booking" class="hero-cta">احجز الآن <i class="fas fa-arrow-left" aria-hidden="true"></i></a>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" class="swiper-button-next" aria-label="الشريحة التالية"></button>
        <button type="button" class="swiper-button-prev" aria-label="الشريحة السابقة"></button>
    </div>
</section>
