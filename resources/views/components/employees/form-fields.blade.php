@props([
'isEdit' => false,
])

<div class="space-y-6">
    <x-ui.card title="المعلومات الشخصية" description="بيانات التواصل الأساسية للموظف.">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-ui.input
                label="الاسم"
                name="form.name"
                wire:model.blur="form.name"
                :error="$errors->first('form.name')"
                autocomplete="name"
                required />

            <x-ui.input
                label="البريد الإلكتروني"
                name="form.email"
                type="email"
                wire:model.blur="form.email"
                :error="$errors->first('form.email')"
                autocomplete="email"
                required />

            <x-ui.input
                label="رقم الهاتف"
                name="form.phone"
                type="tel"
                wire:model.blur="form.phone"
                :error="$errors->first('form.phone')"
                autocomplete="tel"
                required />


        </div>
    </x-ui.card>


    @unless ($isEdit)
        <x-ui.card
            title="تعيين كلمة المرور"
            description="لن تحتاج إلى إنشاء كلمة مرور للموظف من لوحة التحكم."
        >
            <div class="flex items-start gap-4 rounded-xl border border-emerald-100 bg-emerald-50/70 p-4 sm:p-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 shadow-sm">
                    <i class="fas fa-envelope-open-text text-lg" aria-hidden="true"></i>
                </span>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900">الموظف هيحدد كلمة المرور بنفسه</p>
                    <p class="mt-1 text-sm leading-6 text-gray-600">بعد إنشاء الحساب، هنرسل رسالة إلى بريده الإلكتروني تحتوي على رابط آمن لتعيين كلمة المرور. الرابط صالح لمدة 60 دقيقة ويُستخدم مرة واحدة.</p>
                </div>
            </div>
        </x-ui.card>
    @endunless

    @if ($isEdit)
        <x-ui.card
            title="تغيير كلمة المرور"
            description="حمايةً للحساب، لا يتم تغيير كلمة مرور موظف قائم مباشرة من لوحة التحكم."
        >
            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <input
                    type="checkbox"
                    wire:model="form.requestPasswordChange"
                    class="mt-1 h-4 w-4 rounded border-gray-300 text-amber-700 focus:ring-amber-500"
                >
                <span>
                    <span class="block font-semibold text-gray-900">إرسال طلب تغيير كلمة المرور إلى الموظف</span>
                    <span class="mt-1 block text-sm leading-6 text-gray-600">سيصل إلى بريده رابط للموافقة أو الرفض. عند الموافقة سيستلم رابطًا آمنًا ليختار كلمة مرور جديدة بنفسه.</span>
                </span>
            </label>
            @error('form.requestPasswordChange')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('requestPasswordChange')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </x-ui.card>
    @endif

    <x-ui.card title="معلومات التوظيف" description="حدد حالة الموظف داخل النظام.">
        <div class="max-w-md">
            <x-ui.select
                label="حالة الموظف"
                name="form.status"
                wire:model="form.status"
                :error="$errors->first('form.status')"
                required>
                <option value="active">يعمل</option>
                <option value="inactive">لا يعمل</option>
            </x-ui.select>
        </div>
    </x-ui.card>
</div>