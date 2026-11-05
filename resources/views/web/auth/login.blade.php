
@extends('web.layout.app')

@section('content')

<section lang="ar" dir="rtl" class="min-h-screen w-full bg-[#ffefde]">

    <div class="container mx-auto flex min-h-screen max-w-7xl flex-col-reverse items-center px-4 sm:px-6 lg:flex-row lg:px-8">

        {{-- Login Form --}}
        <div class="flex w-full items-center justify-center py-10 sm:py-14 lg:min-h-screen lg:w-1/2 lg:px-8">

            <div class="w-full max-w-md">

                <h1 class="mb-3 text-3xl font-bold text-gray-900 sm:text-4xl">
                    مرحبًا بعودتك
                </h1>

                <p class="mb-8 text-sm leading-7 text-gray-600 sm:text-base">
                    سجّل الدخول للمتابعة إلى حسابك وإدارة حجوزاتك.
                </p>

                @if (session('status'))
                    <div role="status" class="mb-5 rounded-xl border border-green-200 bg-green-50 p-3 text-sm leading-6 text-green-800">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('error'))
    <div
        role="alert"
        class="mb-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm leading-6 text-red-700"
    >
        {{ session('error') }}
    </div>
@endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">

                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-gray-700 sm:text-base">
                            البريد الإلكتروني
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="أدخل بريدك الإلكتروني"
                            autocomplete="email"
                            dir="ltr"
                            required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-left outline-none transition focus:border-amber-600 focus:ring-2 focus:ring-amber-100"
                        >

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-gray-700 sm:text-base">
                            كلمة المرور
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="أدخل كلمة المرور"
                            autocomplete="current-password"
                            dir="ltr"
                            required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-left outline-none transition focus:border-amber-600 focus:ring-2 focus:ring-amber-100"
                        >

                        @error('password')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember Me / Forgot Password --}}
                    <div class="flex flex-wrap items-center justify-between gap-3">

                        <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700">

                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                @checked(old('remember'))
                                class="h-4 w-4 rounded border-gray-300 accent-amber-700"
                            >

                            تذكرني

                        </label>

                        <a href="{{ route('password.request', ['type' => 'customer']) }}" class="text-sm font-semibold text-amber-700 transition hover:text-amber-900">
                            نسيت كلمة المرور؟
                        </a>

                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-amber-700 py-3.5 font-semibold text-white transition hover:bg-amber-800 focus:outline-none focus:ring-4 focus:ring-amber-200"
                    >
                        تسجيل الدخول
                    </button>

                </form>

                <x-auth.google-button />
                {{-- Register Link --}}
                <div class="mt-7 text-center">

                    <p class="text-sm text-gray-600 sm:text-base">

                        ليس لديك حساب؟

                        <a
                            href="{{ route('register') }}"
                            class="font-semibold text-amber-700 transition hover:text-amber-900"
                        >
                            إنشاء حساب جديد
                        </a>

                    </p>

                </div>

            </div>

        </div>

        {{-- Logo / Image --}}
        <div class="flex w-full items-center justify-center py-6 lg:min-h-screen lg:w-1/2 lg:py-10">

            @php
                $brandLogoPath = file_exists(public_path('images/imagelogo.webp'))
                    ? 'images/imagelogo.webp'
                    : (file_exists(public_path('images/imagelogo.webp'))
                        ? 'images/imagelogo.webp'
                        : 'images/imagelogo.webp');
            @endphp

            @if (file_exists(public_path($brandLogoPath)))

                <img
                    src="{{ asset($brandLogoPath) }}"
                    alt="باربر هاوس"
                    class="w-44 max-w-full rounded-3xl object-contain shadow-xl sm:w-60 lg:w-[80%]"
                >

            @else

                <div class="flex aspect-square w-44 items-center justify-center rounded-3xl bg-gradient-to-br from-[#ead9cf] via-[#b9866d] to-[#5b3025] text-white shadow-xl sm:w-60 lg:w-[80%]">

                    <i class="fas fa-cut text-6xl sm:text-7xl" aria-hidden="true"></i>

                </div>

            @endif

        </div>

    </div>

</section>

@endsection
