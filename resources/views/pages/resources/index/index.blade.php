<div dir="rtl" class="space-y-6 px-4 sm:px-6 lg:px-8">
 @php
    $isAdmin = auth('employee')->user()?->hasRole('admin') ?? false;
@endphp

    <x-ui.page-header
        title="الموارد والمخزون"
        description="تابع الكريمات والمستهلكات والكميات المتبقية. عند فتح عبوة جديدة من نفس المورد، يتم إنهاء العبوة السابقة وخصمها تلقائيًا.">
        <x-slot:actions>
            @if ($isAdmin)
            <x-ui.action href="{{ route('admin.resources.create') }}"  variant="primary">
                إضافة مورد
            </x-ui.action>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-3">
        <x-ui.alert type="success" :message="session('message')" />
        <x-ui.alert type="danger" :message="session('error')" />
        <x-ui.alert type="warning" :message="session('warning')" />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-ui.card>
            <p class="text-sm text-gray-500">أنواع الموارد</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($this->stats['items']) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm text-gray-500">إجمالي الوحدات بالمخزون</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($this->stats['quantity']) }}</p>
            <p class="mt-1 text-xs text-gray-500">يشمل الوحدة المفتوحة قيد الاستخدام</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm text-gray-500">مخزون منخفض</p>
            <p class="mt-2 text-2xl font-bold text-amber-600">{{ number_format($this->stats['low_stock']) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm text-gray-500">نفد المخزون</p>
            <p class="mt-2 text-2xl font-bold text-red-600">{{ number_format($this->stats['out_of_stock']) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm text-gray-500">القيمة التقريبية للمخزون</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($this->stats['stock_value'], 2) }} ج.م.</p>
        </x-ui.card>
    </div>

    <x-ui.card title="البحث والتصفية" description="ابحث بالاسم أو كود المورد أو التفاصيل، أو اعرض الموارد حسب حالة مخزونها.">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_220px_140px]">
            <x-ui.input
                label="بحث"
                name="search"
                type="search"
                placeholder="اسم المورد أو الكود أو التفاصيل..."
                wire:model.live.debounce.300ms="search" />

            <div class="space-y-1.5">
                <label for="resource-stock-filter" class="block text-sm font-medium text-gray-700">حالة المخزون</label>
                <select
                    id="resource-stock-filter"
                    wire:model.live="stockFilter"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="all">كل الموارد</option>
                    <option value="in_use">قيد الاستخدام</option>
                    <option value="low">مخزون منخفض</option>
                    <option value="out">نفد المخزون</option>
                    <option value="inactive">موارد معطلة</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="resource-per-page" class="block text-sm font-medium text-gray-700">عدد النتائج</label>
                <select
                    id="resource-per-page"
                    wire:model.live="perPage"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <x-ui.action variant="secondary" wire:click="resetFilters">إعادة تعيين</x-ui.action>

            @if ($isAdmin && count($selected) > 0)
            <x-ui.action
                variant="danger"
                wire:click="bulkDelete"
                wire:confirm="سيتم حذف الموارد التي لا تحتوي على كمية أو سجل استخدام فقط. هل تريد المتابعة؟">
                حذف المحدد ({{ count($selected) }})
            </x-ui.action>
            @endif
        </div>
    </x-ui.card>

    <x-ui.card title="قائمة الموارد">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-right text-xs font-semibold tracking-wide text-gray-600">
                    <tr>
                        @if ($isAdmin)
                        <th scope="col" class="w-12 px-4 py-3">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                aria-label="تحديد موارد الصفحة الحالية"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        @endif
                        <th scope="col" class="px-4 py-3">
                            <button type="button" wire:click="sortBy('name')" class="inline-flex items-center gap-2 hover:text-indigo-700">
                                المورد
                                @if ($sortField === 'name') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3">السعر / الوحدة</th>
                        <th scope="col" class="px-4 py-3">
                            <button type="button" wire:click="sortBy('quantity')" class="inline-flex items-center gap-2 hover:text-indigo-700">
                                الكمية
                                @if ($sortField === 'quantity') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                            </button>
                        </th>
                        <th scope="col" class="px-4 py-3">حالة الاستخدام</th>
                        <th scope="col" class="px-4 py-3">حالة المخزون</th>
                        <th scope="col" class="px-4 py-3 text-center">الإجراءات</th>
                    </tr>
                </thead>

           <tbody class="divide-y divide-gray-100 bg-white">
    @if ($this->items->count() === 0)
        <tr>
            <td
                colspan="{{ $isAdmin ? 7 : 6 }}"
                class="px-4 py-12 text-center"
            >
                <p class="font-semibold text-gray-800">
                    لا توجد موارد مطابقة
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    غيّر كلمات البحث أو أضف موردًا جديدًا.
                </p>
            </td>
        </tr>
    @else
        @foreach ($this->items as $item)
            @php
                $availableUnits = max(
                    0,
                    $item->quantity - ($item->currentUsage ? 1 : 0)
                );

                $isLowStock = $item->quantity > 0
                    && $item->quantity <= $item->minimum_quantity;
            @endphp

            <tr wire:key="inventory-item-{{ $item->id }}" class="align-top transition hover:bg-gray-50">
                @if ($isAdmin)
                    <td class="px-4 py-4">
                        <input
                            type="checkbox"
                            wire:model.live="selected"
                            value="{{ $item->id }}"
                            aria-label="تحديد {{ $item->name }}"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                @endif

                <td class="max-w-sm px-4 py-4">
                    <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                    @if ($item->sku)
                        <p class="mt-1 text-xs text-gray-500">الكود: {{ $item->sku }}</p>
                    @endif
                    @if ($item->description)
                        <p class="mt-2 whitespace-normal text-xs leading-5 text-gray-600">{{ $item->description }}</p>
                    @endif
                    <p class="mt-1 text-xs text-gray-500">حد التنبيه: {{ $item->minimum_quantity }} {{ $item->unit }}</p>
                </td>

                <td class="whitespace-nowrap px-4 py-4">
                    <p class="font-semibold text-gray-900">{{ number_format((float) $item->unit_price, 2) }} ج.م.</p>
                    <p class="mt-1 text-xs text-gray-500">لكل {{ $item->unit }}</p>
                </td>

                <td class="whitespace-nowrap px-4 py-4">
                    <p class="font-semibold text-gray-900">{{ $item->quantity }} {{ $item->unit }}</p>
                    <p class="mt-1 text-xs text-gray-500">غير مفتوح: {{ $availableUnits }}</p>
                </td>

                <td class="min-w-48 px-4 py-4">
                    @if ($item->currentUsage)
                        <x-ui.badge variant="warning">قيد الاستخدام</x-ui.badge>
                        <p class="mt-2 text-xs text-gray-600">
                            بدأه: {{ $item->currentUsage->startedBy?->name ?? 'موظف سابق' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $item->currentUsage->started_at?->format('Y-m-d h:i A') }}
                        </p>
                    @else
                        <x-ui.badge variant="neutral">غير مستخدم حاليًا</x-ui.badge>
                    @endif
                </td>

                <td class="whitespace-nowrap px-4 py-4">
                    @if (! $item->is_active)
                        <x-ui.badge variant="neutral">معطل</x-ui.badge>
                    @elseif ((int) $item->quantity === 0)
                        <x-ui.badge variant="danger">نفد المخزون</x-ui.badge>
                    @elseif ($isLowStock)
                        <x-ui.badge variant="warning">مخزون منخفض</x-ui.badge>
                    @else
                        <x-ui.badge variant="success">متوفر</x-ui.badge>
                    @endif
                </td>

                <td class="px-4 py-4">
                    <div class="flex min-w-44 flex-wrap justify-center gap-2">
                        @if ($item->currentUsage)
                            <x-ui.action
                                size="sm"
                                wire:click="finishCurrentUnit({{ $item->id }})"
                                wire:loading.attr="disabled"
                                wire:target="finishCurrentUnit({{ $item->id }})"
                                wire:confirm="هل انتهى استخدام الوحدة الحالية؟ سيتم خصم وحدة من المخزون.">
                                إنهاء الاستخدام
                            </x-ui.action>

                            <x-ui.action
                                size="sm"
                                variant="secondary"
                                :disabled="! $item->is_active || $item->quantity < 2"
                                wire:click="startNewUnit({{ $item->id }})"
                                wire:loading.attr="disabled"
                                wire:target="startNewUnit({{ $item->id }})"
                                wire:confirm="سيتم إنهاء العبوة الحالية وخصمها من المخزون ثم تسجيل عبوة جديدة قيد الاستخدام. هل تريد المتابعة؟">
                                فتح عبوة جديدة
                            </x-ui.action>
                        @elseif ($item->is_active && $item->quantity > 0)
                            <x-ui.action
                                size="sm"
                                wire:click="startNewUnit({{ $item->id }})"
                                wire:loading.attr="disabled"
                                wire:target="startNewUnit({{ $item->id }})">
                                بدء الاستخدام
                            </x-ui.action>
                        @else
                            <span class="text-xs text-gray-500">لا توجد وحدة متاحة للاستخدام</span>
                        @endif

                        <x-ui.action
                            size="sm"
                            variant="ghost"
                            wire:click="showHistory({{ $item->id }})">
                            سجل الاستخدام
                        </x-ui.action>

                        @if ($isAdmin)
                            <x-ui.action
                                size="sm"
                                variant="secondary"
                                href="{{ route('admin.resources.edit', $item) }}"
                                wire:navigate>
                                تعديل
                            </x-ui.action>

                            @if ((int) $item->quantity === 0 && (int) $item->usages_count === 0)
                                <x-ui.action
                                    size="sm"
                                    variant="danger"
                                    wire:click="confirmDelete({{ $item->id }})">
                                    حذف
                                </x-ui.action>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    @endif
</tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $this->items->links() }}
        </div>
    </x-ui.card>

    <x-ui.confirm-modal
        :show="$showDeleteModal"
        title="حذف المورد"
        message="يمكن حذف المورد فقط عندما تكون كميته صفرًا ولا يوجد له سجل استخدام. هل تريد المتابعة؟"
        confirm-text="حذف المورد"
        cancel-text="إلغاء"
        confirm-action="deleteItem"
        cancel-action="closeDeleteModal" />

    @if ($showHistoryModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4" role="dialog" aria-modal="true" aria-labelledby="usage-history-title">
        <button type="button" class="fixed inset-0 bg-gray-900/50" wire:click="closeHistoryModal" aria-label="إغلاق سجل الاستخدام"></button>
        <section class="relative z-10 w-full max-w-3xl rounded-xl bg-white p-5 shadow-xl sm:p-6">
            <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-4">
                <div>
                    <h2 id="usage-history-title" class="text-lg font-semibold text-gray-900">سجل استخدام: {{ $historyItemName }}</h2>
                    <p class="mt-1 text-sm text-gray-500">آخر 20 عملية استخدام، مع اسم من بدأ الاستخدام ومن أنهى الوحدة.</p>
                </div>
                <button type="button" wire:click="closeHistoryModal" class="rounded-lg px-3 py-1 text-gray-500 hover:bg-gray-100" aria-label="إغلاق">✕</button>
            </div>

            <div class="mt-4 max-h-[60vh] overflow-y-auto">
                @if ($this->usageHistory->isEmpty())
                <p class="py-8 text-center text-sm text-gray-500">لا يوجد سجل استخدام لهذا المورد حتى الآن.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-right text-xs font-semibold text-gray-600">
                            <tr>
                                <th class="px-3 py-3">الحالة</th>
                                <th class="px-3 py-3">بدأ الاستخدام</th>
                                <th class="px-3 py-3">أنهى الاستخدام</th>
                                <th class="px-3 py-3">وقت البداية</th>
                                <th class="px-3 py-3">وقت النهاية</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($this->usageHistory as $usage)
                            <tr wire:key="inventory-usage-{{ $usage->id }}">
                                <td class="whitespace-nowrap px-3 py-3">
                                    @if ($usage->status === \App\Models\InventoryUsage::STATUS_IN_USE)
                                    <x-ui.badge variant="warning">قيد الاستخدام</x-ui.badge>
                                    @else
                                    <x-ui.badge variant="success">انتهى</x-ui.badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-3">{{ $usage->startedBy?->name ?? 'غير متاح' }}</td>
                                <td class="whitespace-nowrap px-3 py-3">{{ $usage->finishedBy?->name ?? 'لم يُنهَ بعد' }}</td>
                                <td class="whitespace-nowrap px-3 py-3">{{ $usage->started_at?->format('Y-m-d h:i A') }}</td>
                                <td class="whitespace-nowrap px-3 py-3">{{ $usage->finished_at?->format('Y-m-d h:i A') ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <div class="mt-5 flex justify-end">
                <x-ui.action variant="secondary" wire:click="closeHistoryModal">إغلاق</x-ui.action>
            </div>
        </section>
    </div>
    @endif
</div>