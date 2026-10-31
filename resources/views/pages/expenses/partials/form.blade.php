<div class="space-y-6">
    <x-ui.card title="بيانات المصروف" description="سجّل ما تم شراؤه أو دفعه مع التاريخ والقيمة والتصنيف. استخدم الملاحظات لتوضيح سبب المصروف.">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-ui.input
                label="اسم المصروف / المشتريات"
                name="title"
                type="text"
                placeholder="مثال: شراء كريمات وشفرات حلاقة"
                wire:model.blur="title"
                :error="$errors->first('title')"
                required />

            <div class="space-y-1.5" x-data="{
                open: false,
                search: '',
                selected: $wire.entangle('category').live,
                options: @js(\App\Models\Expense::CATEGORIES),
                get filteredOptions() {
                    return Object.entries(this.options).filter(([value, label]) =>
                        label.toLowerCase().includes(this.search.toLowerCase()) || value.toLowerCase().includes(this.search.toLowerCase())
                    );
                },
                get selectedLabel() { return this.options[this.selected] ?? 'اختر التصنيف'; }
            }">
                <label class="block text-sm font-medium text-gray-700">التصنيف <span class="text-red-500">*</span></label>
                <div class="relative">
                    <button type="button" @click="open = !open; search = ''; $nextTick(() => $refs.search.focus())"
                        class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-right text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <span x-text="selectedLabel"></span><span class="text-gray-400">⌄</span>
                    </button>
                    <div x-cloak x-show="open" @click.outside="open = false" class="absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg">
                        <input x-ref="search" x-model="search" type="search" placeholder="ابحث عن تصنيف..."
                            class="mb-2 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
                        <div class="max-h-52 overflow-y-auto">
                            <template x-for="[value, label] in filteredOptions" :key="value">
                                <button type="button" @click="selected = value; open = false; search = ''"
                                    class="block w-full rounded-md px-3 py-2 text-right text-sm hover:bg-indigo-50"
                                    :class="selected === value ? 'bg-indigo-50 font-semibold text-indigo-700' : 'text-gray-700'"
                                    x-text="label"></button>
                            </template>
                            <p x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500">لا توجد نتائج مطابقة.</p>
                        </div>
                    </div>
                </div>
                @error('category') <p class="text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
            </div>

            <x-ui.input
                label="المبلغ بالجنيه المصري"
                name="amount"
                type="number"
                min="0.01"
                max="9999999999.99"
                step="0.01"
                placeholder="0.00"
                wire:model.blur="amount"
                :error="$errors->first('amount')"
                required />

            <x-ui.input
                label="تاريخ المصروف"
                name="expense_date"
                type="date"
                :max="now()->toDateString()"
                wire:model="expense_date"
                :error="$errors->first('expense_date')"
                required />

            <div class="space-y-1.5" x-data="{
                open: false,
                search: '',
                selected: $wire.entangle('payment_method').live,
                options: @js(\App\Models\Expense::PAYMENT_METHODS),
                get filteredOptions() {
                    return Object.entries(this.options).filter(([value, label]) =>
                        label.toLowerCase().includes(this.search.toLowerCase()) || value.toLowerCase().includes(this.search.toLowerCase())
                    );
                },
                get selectedLabel() { return this.options[this.selected] ?? 'اختر طريقة الدفع'; }
            }">
                <label class="block text-sm font-medium text-gray-700">طريقة الدفع <span class="text-red-500">*</span></label>
                <div class="relative">
                    <button type="button" @click="open = !open; search = ''; $nextTick(() => $refs.search.focus())"
                        class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-right text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <span x-text="selectedLabel"></span><span class="text-gray-400">⌄</span>
                    </button>
                    <div x-cloak x-show="open" @click.outside="open = false" class="absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg">
                        <input x-ref="search" x-model="search" type="search" placeholder="ابحث عن طريقة دفع..."
                            class="mb-2 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
                        <div class="max-h-52 overflow-y-auto">
                            <template x-for="[value, label] in filteredOptions" :key="value">
                                <button type="button" @click="selected = value; open = false; search = ''"
                                    class="block w-full rounded-md px-3 py-2 text-right text-sm hover:bg-indigo-50"
                                    :class="selected === value ? 'bg-indigo-50 font-semibold text-indigo-700' : 'text-gray-700'"
                                    x-text="label"></button>
                            </template>
                            <p x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500">لا توجد نتائج مطابقة.</p>
                        </div>
                    </div>
                </div>
                @error('payment_method') <p class="text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
            </div>

            <x-ui.input
                label="المورد أو المستفيد (اختياري)"
                name="vendor"
                type="text"
                placeholder="اسم المحل أو الشخص أو الشركة"
                wire:model.blur="vendor"
                :error="$errors->first('vendor')" />

            <x-ui.input
                label="رقم الفاتورة / الإيصال (اختياري)"
                name="receipt_reference"
                type="text"
                placeholder="رقم الإيصال إن وجد"
                wire:model.blur="receipt_reference"
                :error="$errors->first('receipt_reference')" />
        </div>


        <div class="mt-5 rounded-lg border border-indigo-100 bg-indigo-50/60 p-4">
            <div class="mb-3">
                <h3 class="text-sm font-semibold text-gray-900">ربط المصروف بالمخزون (اختياري)</h3>
                <p class="mt-1 text-sm text-gray-600">لو المصروف مقابل شراء مورد موجود في صفحة الموارد، اختاره واكتب الكمية. الكمية هتتضاف للمخزون تلقائيًا عند الحفظ.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="space-y-1.5" x-data="{
                    open: false,
                    search: '',
                    selected: $wire.entangle('inventory_item_id').live,
                    options: @js($inventoryOptions),
                    get filteredOptions() {
                        const term = this.search.toLowerCase();
                        return Object.entries(this.options).filter(([value, label]) =>
                            label.toLowerCase().includes(term) || value.toLowerCase().includes(term)
                        );
                    },
                    get selectedLabel() {
                        return this.selected === null || this.selected === ''
                            ? 'مصروف عادي، لا يؤثر على المخزون'
                            : (this.options[String(this.selected)] ?? 'المورد المحدد');
                    }
                }">
                    <label class="block text-sm font-medium text-gray-700">مورد المخزون</label>
                    <div class="relative">
                        <button type="button"
                            @click="open = !open; search = ''; if (open) $nextTick(() => $refs.inventorySearch.focus())"
                            @if ($inventoryItemLocked) disabled @endif
                            class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-right text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 disabled:cursor-not-allowed disabled:bg-gray-100">
                            <span x-text="selectedLabel"></span><span class="text-gray-400">⌄</span>
                        </button>
                        <div x-cloak x-show="open" @click.outside="open = false" class="absolute z-30 mt-1 w-full rounded-lg border border-gray-200 bg-white p-2 shadow-lg">
                            <input x-ref="inventorySearch" x-model="search" type="search" placeholder="ابحث باسم المورد أو الكود..."
                                class="mb-2 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
                            <button type="button" @click="selected = null; open = false; search = ''"
                                class="mb-1 block w-full rounded-md px-3 py-2 text-right text-sm hover:bg-gray-100"
                                :class="selected === null || selected === '' ? 'font-semibold text-indigo-700' : 'text-gray-700'">
                                مصروف عادي، لا يؤثر على المخزون
                            </button>
                            <div class="max-h-52 overflow-y-auto">
                                <template x-for="[value, label] in filteredOptions" :key="value">
                                    <button type="button" @click="selected = value; open = false; search = ''"
                                        class="block w-full rounded-md px-3 py-2 text-right text-sm hover:bg-indigo-50"
                                        :class="String(selected) === value ? 'bg-indigo-50 font-semibold text-indigo-700' : 'text-gray-700'"
                                        x-text="label"></button>
                                </template>
                                <p x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500">لا توجد موارد مطابقة.</p>
                            </div>
                        </div>
                    </div>
                    @error('inventory_item_id') <p class="text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
                </div>

                @if ($inventory_item_id !== null)
                    <div class="space-y-1.5">
                        <x-ui.input
                            label="الكمية المشتراة"
                            name="inventory_quantity"
                            type="number"
                            min="1"
                            max="100000000"
                            step="1"
                            placeholder="مثال: 5"
                            wire:model.blur="inventory_quantity"
                            :error="$errors->first('inventory_quantity')"
                            required />
                    </div>
                @endif
            </div>

            @if ($inventoryItemLocked)
                <p class="mt-3 text-xs leading-5 text-gray-600">تم ربط هذا المصروف بمورد مخزون بالفعل. المورد ثابت للحفاظ على السجل، وأي تعديل في الكمية سيغيّر الرصيد بالفرق فقط. لن يُسمح بتقليل الكمية إذا كان الرصيد الحالي لا يكفي.</p>
            @else
                <p class="mt-3 text-xs leading-5 text-gray-600">لو المصروف مش شراء مخزون، سيب الاختيار على «مصروف عادي». كل مصروف يمكن ربطه بمورد واحد؛ سجّل كل صنف مخزون في عملية منفصلة عشان الرصيد يفضل دقيقًا.</p>
            @endif
        </div>

        <div class="mt-5 space-y-1.5">
            <label for="expense-notes" class="block text-sm font-medium text-gray-700">التفاصيل والملاحظات (اختياري)</label>
            <textarea
                id="expense-notes"
                wire:model.blur="notes"
                rows="4"
                maxlength="5000"
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                placeholder="اكتب تفاصيل تساعدك تراجع المصروف لاحقًا..."></textarea>
            @error('notes') <p class="text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 border-t border-gray-100 pt-5">
            <label for="receipt-images" class="block text-sm font-medium text-gray-700">صور الإيصال أو الفاتورة (اختياري)</label>
            <p class="mt-1 text-xs text-gray-500">يمكنك إرفاق حتى 5 صور، بحد أقصى 5 ميجابايت للصورة. الأنواع المسموحة: JPG وPNG وWEBP.</p>
            <input id="receipt-images" type="file" wire:model="receiptImages" multiple accept="image/jpeg,image/png,image/webp"
                class="mt-3 block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:ml-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2.5 file:font-medium" />
            @error('receiptImages') <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p> @enderror
            @foreach ($errors->get('receiptImages.*') as $messages)
                @foreach ($messages as $message)
                    <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                @endforeach
            @endforeach

            @if (!empty($existingReceiptImages))
                <div class="mt-4">
                    <p class="mb-2 text-sm font-medium text-gray-700">الصور المحفوظة</p>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                        @foreach ($existingReceiptImages as $imageIndex => $imagePath)
                            <div wire:key="receipt-existing-{{ $imageIndex }}" class="relative overflow-hidden rounded-lg border border-gray-200">
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath) }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath) }}" alt="صورة إيصال" class="h-28 w-full object-cover" />
                                </a>
                                <button type="button" wire:click="removeExistingReceiptImage({{ $imageIndex }})" wire:confirm="هل تريد حذف هذه الصورة عند حفظ التعديلات؟"
                                    class="w-full bg-red-50 px-2 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100">إزالة الصورة</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($receiptImages))
                <div class="mt-4">
                    <p class="mb-2 text-sm font-medium text-gray-700">الصور الجديدة المختارة</p>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                        @foreach ($receiptImages as $image)
                            <div wire:key="receipt-upload-{{ $loop->index }}" class="overflow-hidden rounded-lg border border-gray-200">
                                <img src="{{ $image->temporaryUrl() }}" alt="معاينة صورة الإيصال" class="h-28 w-full object-cover" />
                                <p class="truncate px-2 py-1.5 text-xs text-gray-500">{{ $image->getClientOriginalName() }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div wire:loading wire:target="receiptImages" class="mt-2 text-sm text-indigo-600">جارٍ رفع الصور...</div>
        </div>
    </x-ui.card>

    <div class="flex flex-wrap justify-end gap-3">
        <x-ui.action variant="secondary" href="{{ route('admin.expenses.index') }}" wire:navigate>إلغاء والرجوع</x-ui.action>
        <x-ui.action type="submit" wire:loading.attr="disabled" wire:target="save,update">
            <span wire:loading.remove wire:target="save,update">{{ $submitLabel }}</span>
            <span wire:loading wire:target="save,update">جارٍ الحفظ...</span>
        </x-ui.action>
    </div>
</div>
