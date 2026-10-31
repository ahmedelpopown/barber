@extends('web.layout.app')

@section('content')
<section dir="rtl" class="flex min-h-screen w-full items-center justify-center bg-gradient-to-b from-[#fff5eb] via-[#ffefde] to-[#faf7f4] px-4 py-10 sm:px-6">
    <div class="w-full max-w-xl overflow-hidden rounded-[28px] border border-[#eadbd1] bg-white shadow-[0_24px_80px_rgba(67,42,31,.12)]">
        @if ($valid)
            <div class="border-b border-[#f0e5dd] bg-gradient-to-l from-[#fffaf6] to-white px-6 py-7 sm:px-9 sm:py-8">
                @include('partials.brand-identity')
                <div class="mb-4 mt-5"></div>
                <h1 class="text-2xl font-extrabold text-[#2d201c] sm:text-3xl">مراجعة طلب تغيير كلمة المرور</h1>
                <p class="mt-3 leading-7 text-gray-600">مرحبًا {{ $accountName }}، وصلك طلب من {{ $requesterName }} لبدء إجراء تغيير كلمة المرور الخاصة بحسابك.</p>
            </div>

            <div class="px-6 py-7 sm:px-9 sm:py-8">
                <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-sm leading-7 text-amber-900">
                    <strong class="mb-1 block">مهم قبل اتخاذ القرار</strong>
                    الموافقة لا تغيّر كلمة المرور تلقائيًا. بعد الموافقة سنرسل لك رسالة منفصلة تحتوي على رابط آمن لتختار كلمة مرور جديدة بنفسك. عند الرفض سيظل حسابك كما هو.
                </div>

                @if ($errors->any())
                    <div role="alert" class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm leading-7 text-red-700">
                        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password-change.decide', ['token' => $token]) }}" class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @csrf
                    <button name="decision" value="approved" class="rounded-2xl bg-[#6e3c2d] px-4 py-3.5 font-bold text-white shadow-md shadow-[#6e3c2d]/15 transition hover:-translate-y-0.5 hover:bg-[#593024] focus:outline-none focus:ring-4 focus:ring-[#9a6650]/20">
                        موافق، أريد المتابعة
                    </button>
                    <button name="decision" value="rejected" class="rounded-2xl border border-red-200 bg-white px-4 py-3.5 font-bold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-100">
                        لا أوافق على الطلب
                    </button>
                </form>
                <p class="mt-5 text-center text-xs leading-6 text-gray-500">إذا لم تتوقع هذا الطلب، ارفضه ولا تشارك رابط الرسالة مع أي شخص.</p>
            </div>
        @else
            <div class="px-6 py-9 text-center sm:px-9 sm:py-12">
                @include('partials.brand-identity')
                <span class="mx-auto mb-5 mt-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-2xl text-red-700" aria-hidden="true">!</span>
                <h1 class="text-2xl font-extrabold text-[#2d201c]">الرابط غير متاح</h1>
                <p class="mt-3 leading-7 text-gray-600">هذا الطلب غير صالح أو انتهت صلاحيته أو تم الرد عليه من قبل.</p>
                <a href="{{ route('home') }}" class="mt-7 inline-flex rounded-2xl bg-[#6e3c2d] px-6 py-3 font-bold text-white transition hover:bg-[#593024]">العودة إلى الموقع</a>
            </div>
        @endif
    </div>
</section>
@endsection
