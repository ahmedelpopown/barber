
@extends('web.layout.app')

@section('content')

<section lang="ar" dir="rtl" class="min-h-screen w-full bg-[#ffefde] py-8 sm:py-12">

    <div class="container mx-auto flex max-w-7xl flex-col-reverse items-center gap-8 px-4 sm:px-6 lg:flex-row lg:gap-12 lg:px-8">

        {{-- Register Form --}}
        <div class="flex w-full items-center justify-center lg:w-1/2">

            <div class="w-full max-w-md">

                <h1 class="mb-3 text-3xl font-bold text-gray-900 sm:text-4xl">
                    إنشاء حساب جديد
                </h1>

                <p class="mb-8 text-sm leading-7 text-gray-600 sm:text-base">
                    انضم إلينا واحجز مواعيدك بسهولة.
                </p>

                <form action="{{ route('register') }}" method="POST" class="space-y-5">

                    @csrf

                    {{-- Full Name --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700 sm:text-base">
                            الاسم بالكامل
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="أدخل اسمك بالكامل"
                            autocomplete="name"
                            required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 outline-none transition focus:border-amber-600 focus:ring-2 focus:ring-amber-100"
                        >

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

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
                            placeholder="أنشئ كلمة مرور"
                            autocomplete="new-password"
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

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 sm:text-base">
                            تأكيد كلمة المرور
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="أعد إدخال كلمة المرور"
                            autocomplete="new-password"
                            dir="ltr"
                            required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-left outline-none transition focus:border-amber-600 focus:ring-2 focus:ring-amber-100"
                        >

                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <p class="text-sm leading-7 text-gray-500">
                        بعد إنشاء حسابك، يمكنك متابعة حجوزاتك وإدارة مواعيدك من صفحة الملف الشخصي.
                    </p>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-amber-700 py-3.5 font-semibold text-white transition hover:bg-amber-800 focus:outline-none focus:ring-4 focus:ring-amber-200"
                    >
                        إنشاء الحساب
                    </button>

                </form>

                <x-auth.google-button />
                {{-- Login Link --}}
                <div class="mt-7 text-center">

                    <p class="text-sm text-gray-600 sm:text-base">

                        لديك حساب بالفعل؟

                        <a
                            href="{{ route('login') }}"
                            class="font-semibold text-amber-700 transition hover:text-amber-900"
                        >
                            تسجيل الدخول
                        </a>

                    </p>

                </div>

            </div>

        </div>

        {{-- Logo / Image --}}
        <div class="flex w-full items-center justify-center py-4 lg:min-h-screen lg:w-1/2">

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
