@extends('web.layout.app')

@section('content')
<section dir="rtl" class="flex min-h-screen w-full items-center justify-center bg-gradient-to-b from-[#fff5eb] via-[#ffefde] to-[#faf7f4] px-4 py-10 sm:px-6">
    <div class="w-full max-w-lg overflow-hidden rounded-[28px] border border-[#eadbd1] bg-white shadow-[0_24px_80px_rgba(67,42,31,.12)]">
        <div class="border-b border-[#f0e5dd] bg-gradient-to-l from-[#fffaf6] to-white px-6 py-7 text-center sm:px-9 sm:py-9">
            @include('partials.brand-identity')
            <div class="mt-5"></div>
            <h1 class="text-2xl font-extrabold text-[#2d201c] sm:text-3xl">تعيين كلمة المرور</h1>
            <p class="mt-3 text-sm leading-7 text-gray-600">اختر كلمة مرور جديدة لحسابك، ثم أكدها في الحقل التالي.</p>
        </div>

        <div class="px-6 py-7 sm:px-9 sm:py-8">
            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm leading-7 text-red-700">
                    @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="type" value="{{ $type }}">

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-gray-800">كلمة المرور الجديدة</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        dir="ltr"
                        autofocus
                        class="w-full rounded-2xl border border-gray-200 bg-[#fdfbf9] px-4 py-3.5 text-left text-gray-900 outline-none transition focus:border-[#9a6650] focus:bg-white focus:ring-4 focus:ring-[#9a6650]/10"
                    >
                    <p class="mt-2 text-xs leading-6 text-gray-500">استخدم 8 أحرف على الأقل، ويفضل المزج بين الحروف والأرقام.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-gray-800">تأكيد كلمة المرور</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        dir="ltr"
                        class="w-full rounded-2xl border border-gray-200 bg-[#fdfbf9] px-4 py-3.5 text-left text-gray-900 outline-none transition focus:border-[#9a6650] focus:bg-white focus:ring-4 focus:ring-[#9a6650]/10"
                    >
                </div>

                <button type="submit" class="w-full rounded-2xl bg-[#6e3c2d] px-5 py-3.5 font-bold text-white shadow-lg shadow-[#6e3c2d]/15 transition hover:-translate-y-0.5 hover:bg-[#593024] focus:outline-none focus:ring-4 focus:ring-[#9a6650]/20">
                    حفظ كلمة المرور
                </button>
            </form>

            <a href="{{ route('password.request', ['type' => $type]) }}" class="mt-6 block text-center text-sm font-bold text-[#80513f] transition hover:text-[#4c2b22]">
                انتهت صلاحية الرابط؟ اطلب رابطًا جديدًا
            </a>
        </div>
    </div>
</section>
@endsection
