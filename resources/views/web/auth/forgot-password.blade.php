@extends('web.layout.app')

@section('content')
<section dir="rtl" class="flex min-h-screen w-full items-center justify-center bg-gradient-to-b from-[#fff5eb] via-[#ffefde] to-[#faf7f4] px-4 py-10 sm:px-6">
    <div class="w-full max-w-lg overflow-hidden rounded-[28px] border border-[#eadbd1] bg-white shadow-[0_24px_80px_rgba(67,42,31,.12)]">
        <div class="border-b border-[#f0e5dd] bg-gradient-to-l from-[#fffaf6] to-white px-6 py-7 text-center sm:px-9 sm:py-9">
            @include('partials.brand-identity')
            <div class="mt-5"></div>
            <h1 class="text-2xl font-extrabold text-[#2d201c] sm:text-3xl">نسيت كلمة المرور؟</h1>
            <p class="mx-auto mt-3 max-w-sm text-sm leading-7 text-gray-600">
                {{ $type === 'employee' ? 'استعادة كلمة مرور حساب الموظف أو الإدارة.' : 'استعادة كلمة مرور حساب العميل.' }}
                أدخل بريدك الإلكتروني وسنرسل لك رابطًا آمنًا لتعيين كلمة مرور جديدة.
            </p>
        </div>

        <div class="px-6 py-7 sm:px-9 sm:py-8">
            @if (session('status'))
                <div role="status" class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm leading-7 text-emerald-800">
                    <span class="font-bold" aria-hidden="true">✓</span>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm leading-7 text-red-700">
                    @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-gray-800">البريد الإلكتروني</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        dir="ltr"
                        value="{{ old('email') }}"
                        placeholder="name@example.com"
                        class="w-full rounded-2xl border border-gray-200 bg-[#fdfbf9] px-4 py-3.5 text-left text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-[#9a6650] focus:bg-white focus:ring-4 focus:ring-[#9a6650]/10"
                    >
                </div>

                <button type="submit" class="w-full rounded-2xl bg-[#6e3c2d] px-5 py-3.5 font-bold text-white shadow-lg shadow-[#6e3c2d]/15 transition hover:-translate-y-0.5 hover:bg-[#593024] focus:outline-none focus:ring-4 focus:ring-[#9a6650]/20">
                    إرسال رابط تعيين كلمة المرور
                </button>
            </form>

            <a href="{{ $type === 'employee' ? route('employee.login') : route('login') }}" class="mt-6 flex items-center justify-center gap-2 text-sm font-bold text-[#80513f] transition hover:text-[#4c2b22]">
                <span aria-hidden="true">→</span>
                العودة إلى تسجيل الدخول
            </a>
        </div>
    </div>
</section>
@endsection
