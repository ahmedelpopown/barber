<div dir="rtl" class="space-y-6 px-4 sm:px-6 lg:px-8">
    <x-ui.page-header
        title="الخدمات"
        description="إدارة الخدمات الرئيسية والخدمات الفرعية والمدد والأسعار."
    >
        <x-slot:actions>
            <x-ui.action href="{{ route('admin.services.create') }}" wire:navigate>
                إضافة خدمة
            </x-ui.action>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-3">
        <x-ui.alert type="success" :message="session('message')" />
        <x-ui.alert type="danger" :message="session('error')" />
        <x-ui.alert type="warning" :message="session('warning')" />
    </div>

    <x-ui.card title="البحث والتصفية" description="ابحث باسم الخدمة الرئيسية أو الفرعية، ثم اضبط النتائج. ">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-ui.input
                label="بحث عام"
                name="search"
                type="search"
                placeholder="ابحث عن خدمة أو خدمة فرعية..."
                wire:model.live.debounce.300ms="search"
            />

            <x-ui.input
                label="الخدمة الرئيسية"
                name="name"
                placeholder="اسم الخدمة الرئيسية"
                wire:model.live.debounce.300ms="name"
            />

            <x-ui.input
                label="الخدمة الفرعية"
                name="sup_service"
                placeholder="اسم الخدمة الفرعية"
                wire:model.live.debounce.300ms="sup_service"
            />
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <x-ui.action variant="secondary" wire:click="resetFilters">
                إعادة تعيين
            </x-ui.action>

            @if (count($selected) > 0)
                <x-ui.action
                    variant="danger"
                    wire:click="bulkDelete"
                    wire:confirm="هل تريد حذف الخدمات المحددة؟ لا يمكن التراجع عن هذا الإجراء."
                >
                    حذف المحدد ({{ count($selected) }})
                </x-ui.action>
            @endif
        </div>
    </x-ui.card>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">
                    <tr>
                        <th scope="col" class="w-12 px-4 py-3">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                aria-label="تحديد خدمات الصفحة الحالية"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <button type="button" wire:click="sortBy('name')" class="inline-flex items-center gap-2 hover:text-indigo-700">
                                الخدمة الرئيسية
                                @if ($sortField === 'name')
                                    <span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3">الخدمات الفرعية</th>
                        <th scope="col" class="px-4 py-3 text-center">العدد</th>
                        <th scope="col" class="px-4 py-3 text-center">نطاق السعر</th>
                        <th scope="col" class="px-4 py-3 text-center">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($this->services as $service)
                        <tr wire:key="service-{{ $service->id }}" class="transition hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <input
                                    type="checkbox"
                                    value="{{ $service->id }}"
                                    wire:model.live="selected"
                                    aria-label="تحديد {{ $service->name }}"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 font-semibold text-gray-900">
                                {{ $service->name }}
                            </td>
                            <td class="px-4 py-4">
                                @if ($service->subServices->isNotEmpty())
                                    <div class="flex max-w-xl flex-wrap gap-2">
                                        @foreach ($service->subServices as $subService)
                                            <x-ui.badge variant="info" wire:key="sub-service-{{ $subService->id }}">
                                                {{ $subService->name }}
                                            </x-ui.badge>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">لا توجد خدمات فرعية</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-center text-gray-600">
                                {{ $service->subServices->count() }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-center text-gray-700">
                                @php
                                    $prices = $service->subServices
                                        ->pluck('price')
                                        ->filter(fn ($price) => is_numeric($price))
                                        ->map(fn ($price) => (float) $price);
                                @endphp

                                @if ($prices->isEmpty())
                                    <span class="text-gray-400">غير محدد</span>
                                @elseif ($prices->min() === $prices->max())
                                    {{ number_format($prices->min(), 2) }}
                                @else
                                    <span>{{ number_format($prices->min(), 2) }} – {{ number_format($prices->max(), 2) }}</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <x-ui.action
                                        href="{{ route('admin.services.edit', $service) }}"
                                        wire:navigate
                                        variant="secondary"
                                        size="sm"
                                    >
                                        تعديل
                                    </x-ui.action>
                                    <x-ui.action
                                        variant="danger"
                                        size="sm"
                                        wire:click="confirmDelete({{ $service->id }})"
                                    >
                                        حذف
                                    </x-ui.action>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <p class="font-medium text-gray-700">لا توجد خدمات مطابقة</p>
                                <p class="mt-1 text-sm text-gray-500">جرّب تغيير كلمات البحث أو إعادة تعيين الفلاتر.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 border-t border-gray-100 pt-4">
            {{ $this->services->links() }}
        </div>
    </x-ui.card>

    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4" role="dialog" aria-modal="true" aria-labelledby="delete-service-title">
            <button type="button" class="fixed inset-0 h-full w-full cursor-default bg-gray-900/50" wire:click="closeDeleteModal" aria-label="إغلاق نافذة التأكيد"></button>

            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3l-7.5-13.1a2 2 0 0 0-3.4 0Z" />
                    </svg>
                </div>

                <h2 id="delete-service-title" class="mt-4 text-lg font-semibold text-gray-900">تأكيد حذف الخدمة</h2>
                <p class="mt-2 text-sm leading-6 text-gray-600">
                    هل أنت متأكد من حذف هذه الخدمة؟ قد يؤثر الحذف على البيانات المرتبطة بها وفق قيود قاعدة البيانات.
                </p>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <x-ui.action variant="secondary" wire:click="closeDeleteModal">إلغاء</x-ui.action>
                    <x-ui.action variant="danger" wire:click="deleteService" wire:loading.attr="disabled">تأكيد الحذف</x-ui.action>
                </div>
            </div>
        </div>
    @endif
</div>
