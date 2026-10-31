@props([
    'show' => true,
    'title' => 'تأكيد العملية',
    'message' => 'هل أنت متأكد من تنفيذ هذه العملية؟',
    'confirmText' => 'تأكيد',
    'cancelText' => 'إلغاء',
    'confirmAction' => null,
    'cancelAction' => null,
])

@if ($show)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-modal-title"
    >
        <div class="fixed inset-0 bg-gray-900/50 transition-opacity" aria-hidden="true"></div>

        <div class="relative z-10 w-full max-w-md rounded-xl bg-white p-6 text-right shadow-xl">
            <h2 id="confirm-modal-title" class="text-lg font-semibold text-gray-900">
                {{ $title }}
            </h2>

            <p class="mt-2 text-sm leading-6 text-gray-600">
                {{ $message }}
            </p>

            <div class="mt-6 flex flex-wrap justify-start gap-3">
                @isset($actions)
                    {{ $actions }}
                @else
                    @if ($confirmAction)
                        <x-ui.action
                            variant="danger"
                            wire:click="{{ $confirmAction }}"
                            wire:loading.attr="disabled"
                        >
                            {{ $confirmText }}
                        </x-ui.action>
                    @endif

                    @if ($cancelAction)
                        <x-ui.action
                            variant="secondary"
                            wire:click="{{ $cancelAction }}"
                            wire:loading.attr="disabled"
                        >
                            {{ $cancelText }}
                        </x-ui.action>
                    @endif
                @endisset
            </div>
        </div>
    </div>
@endif
