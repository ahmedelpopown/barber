<x-ui.card title="بيانات المورد" description="أدخل الاسم والتفاصيل والسعر ووحدة القياس.">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <x-ui.input
            label="اسم المورد"
            name="name"
            placeholder="مثال: كريم شعر"
            wire:model.blur="name"
            :error="$errors->first('name')"
            required
        />

        <x-ui.input
            label="كود المورد (اختياري)"
            name="sku"
            placeholder="مثال: CRM-001"
            wire:model.blur="sku"
            :error="$errors->first('sku')"
        />

        <x-ui.input
            label="سعر الوحدة بالجنيه"
            name="unit_price"
            type="number"
            min="0"
            max="9999999999.99"
            step="0.01"
            wire:model.blur="unit_price"
            :error="$errors->first('unit_price')"
            required
        />

        <x-ui.input
            label="وحدة القياس"
            name="unit"
            placeholder="عبوة، قطعة، أنبوبة..."
            wire:model.blur="unit"
            :error="$errors->first('unit')"
            required
        />

        <x-ui.input
            label="الكمية الحالية"
            name="quantity"
            type="number"
            min="0"
            max="100000000"
            step="1"
            wire:model.blur="quantity"
            :error="$errors->first('quantity')"
            required
        />

        <x-ui.input
            label="حد التنبيه للمخزون المنخفض"
            name="minimum_quantity"
            type="number"
            min="0"
            max="100000000"
            step="1"
            wire:model.blur="minimum_quantity"
            :error="$errors->first('minimum_quantity')"
            required
        />

        <div class="space-y-1.5 md:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700">التفاصيل</label>
            <textarea
                id="description"
                name="description"
                rows="4"
                wire:model.blur="description"
                placeholder="اكتب تفاصيل المورد أو ملاحظات التخزين..."
                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                @if ($errors->has('description')) aria-invalid="true" aria-describedby="description-error" @endif
            ></textarea>
            @error('description')
                <p id="description-error" class="text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4">
                <input type="checkbox" wire:model="is_active" class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span>
                    <span class="block text-sm font-medium text-gray-900">المورد نشط</span>
                    <span class="mt-1 block text-sm text-gray-500">المورد المعطّل لا يمكن بدء استخدام وحدة جديدة منه، لكن يمكن إنهاء الوحدة المفتوحة.</span>
                </span>
            </label>
            @error('is_active')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</x-ui.card>

<div class="flex flex-wrap justify-end gap-3">
    <x-ui.action variant="secondary" href="{{ route('admin.resources.index') }}" wire:navigate>
        إلغاء
    </x-ui.action>
    <x-ui.action type="submit" wire:loading.attr="disabled" wire:target="save">
        <span wire:loading.remove wire:target="save">{{ $submitLabel }}</span>
        <span wire:loading wire:target="save">جارٍ الحفظ...</span>
    </x-ui.action>
</div>
