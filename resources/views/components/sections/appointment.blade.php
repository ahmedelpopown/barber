@php
    $appointmentImagePath = 'images/barber-house/booking/appointment-background.webp';
    $legacyAppointmentImagePath = 'images/divider.jpg';
@endphp

<section id="appointment" class="relative flex min-h-[58svh] w-full items-center justify-center overflow-hidden sm:min-h-[65svh]">
    @if (file_exists(public_path($appointmentImagePath)))
        <img
            src="{{ asset($appointmentImagePath) }}"
            class="appointment-image absolute inset-0 h-full w-full object-cover"
            alt=""
            loading="lazy"
            decoding="async"
            aria-hidden="true"
        >
    @elseif (file_exists(public_path($legacyAppointmentImagePath)))
        <img
            src="{{ asset($legacyAppointmentImagePath) }}"
            class="appointment-image absolute inset-0 h-full w-full object-cover"
            alt=""
            loading="lazy"
            decoding="async"
            aria-hidden="true"
        >
    @else
        <div class="appointment-image absolute inset-0 bg-gradient-to-br from-[#231814] via-[#68402f] to-[#9a6c54]" aria-hidden="true"></div>
    @endif

    <div class="absolute inset-0 bg-gradient-to-l from-black/80 via-black/55 to-black/35" aria-hidden="true"></div>

    <div class="appointment-copy relative z-10 flex w-full max-w-4xl flex-col items-center justify-center px-5 py-20 text-center text-white sm:px-8" data-reveal>
        <p class="mb-3 text-xs font-semibold uppercase tracking-[4px] text-[#e2b58e]">APPOINTMENT</p>
        <h2 class="heading-font text-4xl font-light sm:text-6xl md:text-7xl">
            حجزك يبدأ هنا
        </h2>

        <h3 class="mt-7 text-sm font-bold uppercase tracking-[2px] sm:text-lg sm:tracking-[3px]">
            ساعات العمل في الصالون
        </h3>

        <p class="mt-4 text-sm sm:text-lg">الإثنين - السبت: من ١٠ صباحًا حتى ٧ مساءً</p>
        <p class="mt-1 text-sm sm:text-lg">الأحد: من ١٠ صباحًا حتى ٥ مساءً</p>

        <a
            href="#booking"
            class="mt-9 inline-flex rounded-xl border border-[#c98a6a]/60 bg-[#552d25]/90 px-8 py-3.5 text-sm font-semibold uppercase tracking-wider text-white shadow-lg transition duration-300 hover:-translate-y-1 hover:bg-[#6a382c] hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white"
        >
            احجز الآن
        </a>
    </div>
</section>
