@php
    $brandLogoPath = file_exists(public_path('images/Untitled-4.png'))
        ? 'images/Untitled-4.png'
        : (file_exists(public_path('images/imagelogo.webp'))
            ? 'images/imagelogo.webp'
            : 'images/logo4-removebg-preview.png');
@endphp
<div class="flex items-center justify-center gap-3" dir="rtl">
    <img
        src="{{ asset($brandLogoPath) }}"
        alt="شعار باربر هاوس"
        width="68"
        height="68"
        class="h-14 w-14 shrink-0 rounded-2xl object-contain sm:h-16 sm:w-16"
        fetchpriority="high"
    >
    <span class="flex flex-col items-start leading-tight">
        <span class="text-[10px] font-extrabold tracking-[.22em] text-[#a47764] sm:text-xs">BARBER HOUSE</span>
        <span class="mt-1 text-lg font-extrabold text-[#3a2923] sm:text-xl">باربر هاوس</span>
    </span>
</div>
