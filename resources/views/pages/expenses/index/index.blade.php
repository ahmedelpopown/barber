<div dir="rtl" class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header
        title="المصروفات"
        description="سجّل مشتريات المحل ومصروفاته، وراجع إجمالي اليوم والشهر وسجل كل عملية دفع.">
        <x-slot:actions>
            <x-ui.action href="{{ route('admin.expenses.create') }}" wire:navigate>
                <span aria-hidden="true">＋</span> إضافة مصروف
            </x-ui.action>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-3">
        <x-ui.alert type="success" :message="session('message')" />
        <x-ui.alert type="danger" :message="session('error')" />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.card>
            <p class="text-sm text-gray-500">مصروفات اليوم</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($this->stats['today_total'], 2) }} ج.م.</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm text-gray-500">مصروفات الشهر الحالي</p>
            <p class="mt-2 text-2xl font-bold text-indigo-700">{{ number_format($this->stats['month_total'], 2) }} ج.م.</p>
            <p class="mt-1 text-xs text-gray-500">{{ number_format($this->stats['month_count']) }} عملية</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm text-gray-500">عدد المصروفات المسجلة</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($this->stats['total_count']) }}</p>
        </x-ui.card>
        <x-ui.card>
            <p class="text-sm text-gray-500">متوسط مصروف العملية هذا الشهر</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">
                {{ number_format($this->stats['month_count'] > 0 ? $this->stats['month_total'] / $this->stats['month_count'] : 0, 2) }} ج.م.
            </p>
        </x-ui.card>
    </div>

    <x-ui.card title="البحث والتصفية" description="ابحث باسم المصروف أو المورد أو رقم الإيصال، وضيّق النتائج بالتاريخ والتصنيف وطريقة الدفع.">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <x-ui.input label="بحث" name="search" type="search" placeholder="مثال: فاتورة كهرباء أو مورد الكريمات" wire:model.live.debounce.300ms="search" />

            <x-ui.select label="تصنيف المصروف" name="category" wire:model.live="category">
                <option value="all">كل التصنيفات</option>
                @foreach (\App\Models\Expense::CATEGORIES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </x-ui.select>

            <x-ui.select label="طريقة الدفع" name="paymentMethod" wire:model.live="paymentMethod">
                <option value="all">كل طرق الدفع</option>
                @foreach (\App\Models\Expense::PAYMENT_METHODS as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </x-ui.select>

            <x-ui.input label="من تاريخ" name="dateFrom" type="date" wire:model.live="dateFrom" />
            <x-ui.input label="إلى تاريخ" name="dateTo" type="date" wire:model.live="dateTo" />
            <x-ui.select label="عدد النتائج" name="perPage" wire:model.live="perPage">
                <option value="10">10 نتائج</option>
                <option value="25">25 نتيجة</option>
                <option value="50">50 نتيجة</option>
            </x-ui.select>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-ui.action variant="secondary" wire:click="resetFilters">إعادة تعيين الفلاتر</x-ui.action>
        </div>
    </x-ui.card>

    <x-ui.card title="سجل المصروفات">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-right text-xs font-semibold text-gray-600">
                    <tr>
                        <th class="px-4 py-3"><button type="button" wire:click="sortBy('title')" class="hover:text-indigo-700">المصروف @if ($sortField === 'title') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif</button></th>
                        <th class="px-4 py-3"><button type="button" wire:click="sortBy('category')" class="hover:text-indigo-700">التصنيف @if ($sortField === 'category') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif</button></th>
                        <th class="whitespace-nowrap px-4 py-3"><button type="button" wire:click="sortBy('amount')" class="hover:text-indigo-700">المبلغ @if ($sortField === 'amount') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif</button></th>
                        <th class="whitespace-nowrap px-4 py-3"><button type="button" wire:click="sortBy('expense_date')" class="hover:text-indigo-700">التاريخ @if ($sortField === 'expense_date') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif</button></th>
                        <th class="whitespace-nowrap px-4 py-3">طريقة الدفع</th>
                        <th class="px-4 py-3">المورد / المستفيد</th>
                        <th class="whitespace-nowrap px-4 py-3 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($this->expenses as $expense)
                        <tr wire:key="expense-{{ $expense->id }}" class="transition hover:bg-gray-50">
                            <td class="max-w-xs px-4 py-4">
                                <p class="font-semibold text-gray-900">{{ $expense->title }}</p>
                                @if ($expense->receipt_reference)
                                    <p class="mt-1 text-xs text-gray-500">الإيصال: {{ $expense->receipt_reference }}</p>
                                @endif
                                @if ($expense->inventory_item_id && $expense->inventoryItem)
                                    <p class="mt-1 text-xs text-indigo-700">المخزون: {{ $expense->inventoryItem->name }} · {{ $expense->inventory_quantity }} {{ $expense->inventoryItem->unit }}</p>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <x-ui.badge variant="neutral">{{ $expense->category_label }}</x-ui.badge>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 font-semibold text-gray-900">{{ number_format((float) $expense->amount, 2) }} ج.م.</td>
                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">{{ $expense->expense_date?->format('Y-m-d') }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-gray-600">{{ $expense->payment_method_label }}</td>
                            <td class="max-w-xs px-4 py-4 text-gray-600">{{ $expense->vendor ?: '—' }}</td>
                            <td class="px-4 py-4">
                                <div class="flex min-w-48 flex-wrap justify-center gap-2">
                                    <x-ui.action size="sm" variant="ghost" href="{{ route('admin.expenses.show', $expense) }}" wire:navigate>عرض</x-ui.action>
                                    <x-ui.action size="sm" variant="secondary" href="{{ route('admin.expenses.edit', $expense) }}" wire:navigate>تعديل</x-ui.action>
                                    <x-ui.action size="sm" variant="danger" wire:click="confirmDelete({{ $expense->id }})">حذف</x-ui.action>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="font-semibold text-gray-800">لا توجد مصروفات مطابقة</p>
                                <p class="mt-1 text-sm text-gray-500">أضف مصروفًا جديدًا أو غيّر خيارات البحث والتصفية.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $this->expenses->links() }}</div>
    </x-ui.card>

    <x-ui.confirm-modal
        :show="$showDeleteModal"
        title="حذف المصروف"
        message="سيُخفى المصروف من القوائم والإجماليات مع الاحتفاظ بسجله. إذا كان مرتبطًا بالمخزون، ستُخصم كميته من الرصيد بشرط أن تكون الكمية متاحة. هل تريد المتابعة؟"
        confirm-text="حذف المصروف"
        cancel-text="إلغاء"
        confirm-action="deleteExpense"
        cancel-action="closeDeleteModal" />
</div>
