<div dir="rtl" class="space-y-6">
    <x-ui.card title="بيانات الخدمة" description="أدخل اسم الخدمة الرئيسية ثم أضف الخدمات الفرعية وأسعارها ومددها.">
        <x-ui.input
            label="اسم الخدمة"
            name="name"
            :value="$name"
            placeholder="مثال: العناية بالشعر"
            wire:model.blur="name"
            :error="$errors->first('name')"
            required
            maxlength="255"
        />
    </x-ui.card>

    <x-ui.card title="الخدمات الفرعية" description="كل خدمة فرعية لها اسم ومدة وسعر مستقل.">
        <x-slot:header>
            <x-ui.action type="button" wire:click="addSubService" variant="secondary" size="sm">
                <span aria-hidden="true">+</span>
                إضافة خدمة فرعية
            </x-ui.action>
        </x-slot:header>

        @if ($errors->has('subServices'))
            <div class="mb-4">
                <x-ui.alert type="danger" :message="$errors->first('subServices')" />
            </div>
        @endif

        <div class="space-y-4">
            @foreach ($subServices as $index => $subService)
                <section
                    wire:key="sub-service-row-{{ $subService['id'] ?? 'new-' . $index }}"
                    class="rounded-xl border border-gray-200 bg-gray-50 p-4"
                >
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold text-gray-800">الخدمة الفرعية {{ $index + 1 }}</h3>
                        <x-ui.action
                            type="button"
                            variant="danger"
                            size="sm"
                            wire:click="removeSubService({{ $index }})"
                            wire:confirm="هل تريد إزالة هذه الخدمة الفرعية من النموذج؟"
                        >
                            حذف
                        </x-ui.action>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <x-ui.input
                            label="اسم الخدمة الفرعية"
                            name="subServices.{{ $index }}.name"
                            :value="$subService['name'] ?? ''"
                            placeholder="اسم الخدمة الفرعية"
                            wire:model.blur="subServices.{{ $index }}.name"
                            :error="$errors->first('subServices.' . $index . '.name')"
                            required
                            maxlength="255"
                        />

                        <x-ui.input
                            label="المدة بالدقائق"
                            name="subServices.{{ $index }}.duration"
                            type="number"
                            :value="$subService['duration'] ?? ''"
                            placeholder="مثال: 30"
                            min="5"
                            step="1"
                            wire:model.blur="subServices.{{ $index }}.duration"
                            :error="$errors->first('subServices.' . $index . '.duration')"
                            required
                        />

                        <x-ui.input
                            label="السعر"
                            name="subServices.{{ $index }}.price"
                            type="number"
                            :value="$subService['price'] ?? ''"
                            placeholder="0.00"
                            min="0"
                            step="0.01"
                            wire:model.blur="subServices.{{ $index }}.price"
                            :error="$errors->first('subServices.' . $index . '.price')"
                            required
                        />
                    </div>
                </section>
            @endforeach
        </div>
    </x-ui.card>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <x-ui.action
            href="{{ route('admin.services.index') }}"
            wire:navigate
            variant="secondary"
        >
            رجوع
        </x-ui.action>

        <x-ui.action type="submit" wire:loading.attr="disabled">
            <span wire:loading.remove>{{ $button }}</span>
            <span wire:loading>جارٍ الحفظ...</span>
        </x-ui.action>
    </div>
</div>
