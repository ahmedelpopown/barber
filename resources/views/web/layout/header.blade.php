@php
    $isAuthPage = request()->routeIs('login', 'register', 'verification.notice', 'password.*', 'password-change.*');
    $homePrefix = request()->routeIs('home') ? '' : route('home');
    $loginUrl = request()->query('type') === 'employee' ? route('employee.login') : route('login');
    $brandLogoPath = file_exists(public_path('images/Untitled-4.png'))
        ? 'images/Untitled-4.png'
        : (file_exists(public_path('images/imagelogo.webp'))
            ? 'images/imagelogo.webp'
            : 'images/logo4-removebg-preview.png');
@endphp

{{-- Initial page loading transition --}}
<div id="pageLoader" class="page-loader" aria-hidden="true">
    <div class="page-loader__content">
        <span class="page-loader__icon" aria-hidden="true">✂</span>
        <span class="page-loader__brand">باربر هاوس</span>
        <span class="page-loader__line" aria-hidden="true"></span>
    </div>
</div>

<nav
    id="navbar"
    data-auth-page="{{ $isAuthPage ? 'true' : 'false' }}"
    class="fixed inset-x-0 top-0 z-[9999] bg-transparent text-white transition-all duration-300 {{ $isAuthPage ? 'is-auth-page' : '' }}"
>
    <div class="navbar-container relative z-[10002] mx-auto flex max-w-[1400px] items-center justify-between px-4 sm:px-6 lg:px-12">

        {{-- Brand --}}
        <a href="{{ route('home') }}" class="shrink-0" aria-label="الصفحة الرئيسية">
            @if (file_exists(public_path($brandLogoPath)))
                <img
                    src="{{ asset($brandLogoPath) }}"
                    class="logo w-auto object-contain transition-all duration-300"
                    alt="باربر هاوس"
                >
            @else
                <span class="logo logo-fallback flex items-center gap-2 font-bold transition-all duration-300">
                    <span aria-hidden="true">✂</span>
                    باربر هاوس
                </span>
            @endif
        </a>

        {{-- Desktop Navigation --}}
        <ul id="list" class="hidden items-center gap-6 text-sm font-semibold tracking-wide transition-all duration-300 lg:flex xl:gap-8">
            <li><a href="{{ route('home') }}">الرئيسية</a></li>
            <li><a href="{{ $homePrefix }}#about">من نحن</a></li>
            <li><a href="{{ $homePrefix }}#services">خدماتنا</a></li>
            <li><a href="{{ $homePrefix }}#booking">الحجز</a></li>
            <li><a href="{{ $homePrefix }}#prices">الأسعار</a></li>
            <li><a href="{{ $homePrefix }}#contact">تواصل معنا</a></li>

            @auth('customer')
                <li><a href="{{ route('customer.profile') }}">حسابي</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">تسجيل الخروج</button>
                    </form>
                </li>
            @else
                <li><a href="{{ $loginUrl }}">تسجيل الدخول</a></li>
            @endauth
        </ul>

        {{-- Header Actions --}}
        <div class="flex items-center gap-3">
            <a
                href="{{ $homePrefix }}#booking"
                class="hidden rounded bg-[#5b3025] px-6 py-3 font-semibold text-white transition hover:bg-[#734034] lg:inline-flex"
            >
                احجز الآن
            </a>

            <button
                id="menuBtn"
                type="button"
                class="relative z-[10003] flex h-10 w-10 items-center justify-center rounded-lg text-3xl transition-colors duration-300 hover:bg-black/5 lg:hidden"
                aria-label="فتح القائمة"
                aria-controls="mobileMenu"
                aria-expanded="false"
            >
                <span id="menuIcon" aria-hidden="true">☰</span>
            </button>
        </div>
    </div>

    {{-- Animated full-screen mobile menu --}}
    <div
        id="mobileMenu"
        class="mobile-menu hidden"
        aria-hidden="true"
    >
        <ul>
            <li><a class="mobile-menu-link" href="{{ route('home') }}">الرئيسية</a></li>
            <li><a class="mobile-menu-link" href="{{ $homePrefix }}#about">من نحن</a></li>
            <li><a class="mobile-menu-link" href="{{ $homePrefix }}#services">خدماتنا</a></li>
            <li><a class="mobile-menu-link" href="{{ $homePrefix }}#booking">الحجز</a></li>
            <li><a class="mobile-menu-link" href="{{ $homePrefix }}#prices">الأسعار</a></li>
            <li><a class="mobile-menu-link" href="{{ $homePrefix }}#contact">تواصل معنا</a></li>

            @auth('customer')
                <li><a class="mobile-menu-link" href="{{ route('customer.profile') }}">حسابي</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="mobile-menu-link w-full">تسجيل الخروج</button>
                    </form>
                </li>
            @else
                <li><a class="mobile-menu-link" href="{{ $loginUrl }}">تسجيل الدخول</a></li>
            @endauth

            <li class="mobile-menu-cta">
                <a
                    href="{{ $homePrefix }}#booking"
                    class="block w-full rounded-xl bg-[#5b3025] px-6 py-4 text-center font-bold text-white transition hover:bg-[#734034]"
                >
                    احجز الآن
                </a>
            </li>
        </ul>
    </div>
</nav>
