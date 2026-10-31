@extends('web.layout.app')

@section('content')
<section dir="rtl" class="flex min-h-screen w-full items-center justify-center bg-gradient-to-b from-[#fff5eb] via-[#ffefde] to-[#faf7f4] px-4 py-10 sm:px-6">
    <div class="w-full max-w-lg rounded-[28px] border border-[#eadbd1] bg-white px-6 py-9 text-center shadow-[0_24px_80px_rgba(67,42,31,.12)] sm:px-9 sm:py-12">
        @include('partials.brand-identity')
        <span class="mx-auto mb-5 mt-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-2xl font-bold text-emerald-700" aria-hidden="true">✓</span>
        <h1 class="text-2xl font-extrabold text-[#2d201c] sm:text-3xl">تم استلام ردك</h1>
        <p class="mt-4 leading-8 text-gray-600">{{ $message }}</p>
        <a href="{{ route('home') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-[#6e3c2d] px-6 py-3.5 font-bold text-white shadow-lg shadow-[#6e3c2d]/15 transition hover:-translate-y-0.5 hover:bg-[#593024] sm:w-auto">
            العودة إلى الموقع
        </a>
    </div>
</section>
@endsection
