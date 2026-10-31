<?php

use App\Models\Expense;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('تفاصيل المصروف')] class extends Component
{
    public Expense $expense;

    public function mount(Expense $expense): void
    {
        abort_unless(auth('employee')->user()?->hasRole('admin'), 403);
        $this->expense = $expense->load(['createdBy:id,name', 'inventoryItem:id,name,unit']);
    }
};
?>

<div dir="rtl" class="mx-auto max-w-4xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header title="تفاصيل المصروف" description="راجع بيانات المصروف المسجلة والقيمة وطريقة الدفع." />

    <x-ui.card title="{{ $expense->title }}">
        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div><dt class="text-sm text-gray-500">المبلغ</dt><dd class="mt-1 text-xl font-bold text-red-700">{{ number_format((float) $expense->amount, 2) }} ج.م.</dd></div>
            <div><dt class="text-sm text-gray-500">التاريخ</dt><dd class="mt-1 font-semibold text-gray-900">{{ $expense->expense_date->format('Y-m-d') }}</dd></div>
            <div><dt class="text-sm text-gray-500">التصنيف</dt><dd class="mt-1"><x-ui.badge variant="neutral">{{ $expense->category_label }}</x-ui.badge></dd></div>
            <div><dt class="text-sm text-gray-500">طريقة الدفع</dt><dd class="mt-1 font-semibold text-gray-900">{{ $expense->payment_method_label }}</dd></div>
            <div><dt class="text-sm text-gray-500">المورد أو المستفيد</dt><dd class="mt-1 text-gray-900">{{ $expense->vendor ?: 'غير مسجل' }}</dd></div>
            <div><dt class="text-sm text-gray-500">رقم الفاتورة / الإيصال</dt><dd class="mt-1 text-gray-900">{{ $expense->receipt_reference ?: 'غير مسجل' }}</dd></div>
            <div><dt class="text-sm text-gray-500">تأثير المصروف على المخزون</dt><dd class="mt-1 text-gray-900">@if ($expense->inventory_item_id && $expense->inventoryItem) {{ $expense->inventoryItem->name }}، كمية الشراء: {{ number_format((int) $expense->inventory_quantity) }} {{ $expense->inventoryItem->unit }} @else مصروف مالي فقط @endif</dd></div>
            <div><dt class="text-sm text-gray-500">سجل المصروف</dt><dd class="mt-1 text-gray-900">{{ $expense->createdBy?->name ?? 'حساب محذوف أو غير متاح' }}</dd></div>
            <div><dt class="text-sm text-gray-500">تاريخ التسجيل</dt><dd class="mt-1 text-gray-900">{{ $expense->created_at?->format('Y-m-d h:i A') }}</dd></div>
        </dl>

        @if ($expense->receipt_images)
            <div class="mt-6 border-t border-gray-100 pt-5">
                <h2 class="text-sm font-semibold text-gray-700">صور الإيصال أو الفاتورة</h2>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                    @foreach ($expense->receipt_images as $imagePath)
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath) }}" target="_blank" rel="noopener noreferrer" class="overflow-hidden rounded-lg border border-gray-200">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath) }}" alt="صورة إيصال المصروف" class="h-36 w-full object-cover transition hover:scale-105" />
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($expense->notes)
            <div class="mt-6 border-t border-gray-100 pt-5">
                <h2 class="text-sm font-semibold text-gray-700">التفاصيل والملاحظات</h2>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $expense->notes }}</p>
            </div>
        @endif
    </x-ui.card>

    <div class="flex flex-wrap justify-end gap-3">
        <x-ui.action variant="secondary" href="{{ route('admin.expenses.index') }}" wire:navigate>رجوع للمصروفات</x-ui.action>
        <x-ui.action href="{{ route('admin.expenses.edit', $expense) }}" wire:navigate>تعديل المصروف</x-ui.action>
    </div>
</div>
