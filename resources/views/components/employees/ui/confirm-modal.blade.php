@props([
    'show' => false,
    'title' => 'تأكيد الإجراء',
    'message' => 'هل أنت متأكد من تنفيذ هذا الإجراء؟',
])

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
        <div class="fixed inset-0 bg-gray-900/50" aria-hidden="true"></div>

        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <h2 id="confirm-modal-title" class="text-lg font-semibold text-gray-900">
                    {{ $title }}
                </h2>

                <p class="mt-3 text-sm leading-6 text-gray-600">
                    {{ $message }}
                </p>

                @isset($actions)
                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-start">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endif
