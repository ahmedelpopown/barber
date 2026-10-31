@php
    $aboutImagePath = 'images/barber-house/about/shop-interior.webp';
    $legacyAboutImagePath = 'images/about3.png';
@endphp

<section id="about" class="bg-[#fffdfb] px-4 py-16 sm:py-20 lg:py-24">
    <div class="mx-auto flex max-w-7xl flex-col items-center gap-8 lg:flex-row lg:gap-14">
        <div class="about-image-frame w-full max-w-[420px] shrink-0" data-reveal>
            @if (file_exists(public_path($aboutImagePath)))
                <img
                    src="{{ asset($aboutImagePath) }}"
                    alt="داخل صالون باربر هاوس"
                    loading="lazy"
                    decoding="async"
                    data-parallax
                    data-parallax-speed="0.07"
                >
            @elseif (file_exists(public_path($legacyAboutImagePath)))
                <img
                    src="{{ asset($legacyAboutImagePath) }}"
                    alt="داخل صالون باربر هاوس"
                    loading="lazy"
                    decoding="async"
                    data-parallax
                    data-parallax-speed="0.07"
                >
            @else
                <div class="about-image-placeholder flex aspect-[4/3] w-full items-center justify-center bg-gradient-to-br from-[#ead9cf] via-[#9e6a50] to-[#261914] text-white">
                    <i class="fas fa-store text-7xl" aria-hidden="true"></i>
                </div>
            @endif
        </div>

        <div class="w-full max-w-3xl text-center lg:text-right">
            <header data-reveal>
                <p class="text-xs font-semibold uppercase tracking-[4px] text-[#c98a6a]">ABOUT US</p>
                <h2 class="heading-font mt-2 text-4xl text-[#552d25] sm:text-5xl">من نحن</h2>
                <p class="mt-3 text-xs font-semibold uppercase tracking-[4px] text-[#c98a6a] sm:text-sm">
                    قصة التميز تبدأ هنا
                </p>
            </header>

            <div class="mt-7" data-reveal style="--reveal-delay: 120ms">
                <h3 class="text-xl font-semibold leading-relaxed text-[#2d201c] sm:text-2xl">
                    مرحبًا بك في مكان يربط بين الحلاقة الكلاسيكية والذوق الحديث، وبين الراحة والاحترافية.
                </h3>
            </div>

            <div class="mt-6" data-reveal style="--reveal-delay: 200ms">
                <h3 class="mb-3 text-sm font-bold uppercase tracking-[2px] text-[#5d3026] sm:tracking-[3px]">
                    اهتمام بالتفاصيل في كل خدمة
                </h3>
                <p class="text-base leading-8 text-gray-700 sm:text-lg">
                    نحرص على تقديم تجربة حلاقة راقية تجمع بين الإبداع، الدقة، والاهتمام بأدق التفاصيل، لأن كل زيارة هي أكثر من مجرد خدمة؛ بل تجربة تهتم بمظهرك وراحتك.
                </p>
            </div>
        </div>
    </div>
</section>
