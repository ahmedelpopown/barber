
<div class="mt-6">

    {{-- Divider --}}
    <div class="relative flex items-center">
        <div class="flex-grow border-t border-gray-300"></div>

        <span class="mx-4 shrink-0 text-sm text-gray-500">
            أو
        </span>

        <div class="flex-grow border-t border-gray-300"></div>
    </div>

    {{-- Google Button --}}
    <a
        href="{{ route('customer.google.redirect') }}"
        class="mt-6 flex w-full items-center justify-center gap-3 rounded-xl border border-gray-300 bg-white px-4 py-3.5 font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-amber-100"
    >
        <svg
            class="h-5 w-5 shrink-0"
            viewBox="0 0 48 48"
            aria-hidden="true"
        >
            <path fill="#4285F4" d="M43.6 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h11c-.5 2.7-2 5-4.3 6.5v5.4h7c4.1-3.8 6.4-9.4 6.4-15.9z"/>
            <path fill="#34A853" d="M24 44c5.9 0 10.8-2 14.4-5.4l-7-5.4c-1.9 1.3-4.3 2.1-7.4 2.1-5.7 0-10.5-3.8-12.2-8.9H4.6v5.6C8.2 39.1 15.5 44 24 44z"/>
            <path fill="#FBBC05" d="M11.8 26.4c-.4-1.3-.7-2.7-.7-4.4s.3-3.1.7-4.4V12H4.6C3.1 15 2.3 18.4 2.3 22s.8 7 2.3 10l7.2-5.6z"/>
            <path fill="#EA4335" d="M24 8.7c3.2 0 6.1 1.1 8.4 3.3l6.3-6.3C34.8 2.2 29.9 0 24 0 15.5 0 8.2 4.9 4.6 12l7.2 5.6c1.7-5.1 6.5-8.9 12.2-8.9z"/>
        </svg>

        <span>المتابعة باستخدام Google</span>
    </a>

</div>
