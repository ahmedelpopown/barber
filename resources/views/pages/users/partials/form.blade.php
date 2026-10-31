<div dir="rtl" class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header
        :title="$isEdit ? 'تعديل بيانات العميل' : 'إضافة عميل جديد'"
        :description="$isEdit
            ? 'حدّث بيانات التواصل، ويمكنك طلب تغيير كلمة المرور بعد موافقة صاحب الحساب عبر البريد الإلكتروني.'
            : 'أدخل بيانات العميل، وسنرسل إليه رابطًا آمنًا لتعيين كلمة المرور بنفسه.'"
    />

    <div class="space-y-3">
        <x-ui.alert type="danger" :message="$errors->first() ?: session('error')" />
        <x-ui.alert type="success" :message="session('message')" />
    </div>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card
            title="البيانات الأساسية"
            description="الاسم والبريد الإلكتروني ورقم الهاتف المستخدم للتواصل مع العميل."
        >
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-ui.input
                    label="اسم العميل"
                    name="form.name"
                    type="text"
                    placeholder="اكتب الاسم بالكامل"
                    wire:model.blur="form.name"
                    :error="$errors->first('form.name')"
                    autocomplete="name"
                    required
                />

                <x-ui.input
                    label="البريد الإلكتروني"
                    name="form.email"
                    type="email"
                    placeholder="name@example.com"
                    wire:model.blur="form.email"
                    :error="$errors->first('form.email')"
                    autocomplete="email"
                    required
                />

                <x-ui.input
                    label="رقم الهاتف"
                    name="form.phone"
                    type="tel"
                    placeholder="أدخل رقم الهاتف"
                    wire:model.blur="form.phone"
                    :error="$errors->first('form.phone')"
                    autocomplete="tel"
                    required
                />
            </div>
        </x-ui.card>

        <x-ui.card
            title="تعيين كلمة المرور"
            :description="$isEdit
                ? 'لا يمكن تغيير كلمة مرور حساب قائم مباشرة من لوحة التحكم. أرسل طلبًا لصاحب الحساب ليوافق عليه ثم يعيّن كلمة مروره بنفسه.'
                : 'لن نطلب منك إنشاء كلمة مرور للعميل. بعد حفظ الحساب سنرسل إليه رسالة بريد إلكتروني بها رابط آمن ليختار كلمة مروره بنفسه.'"
        >
            @if ($isEdit)
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
                    <input
                        type="checkbox"
                        wire:model="form.requestPasswordChange"
                        class="mt-1 h-4 w-4 shrink-0 rounded border-gray-300 text-amber-700 focus:ring-amber-500"
                    >
                    <span class="min-w-0">
                        <span class="block font-semibold leading-6 text-gray-900">إرسال طلب تغيير كلمة المرور إلى صاحب الحساب</span>
                        <span class="mt-1 block text-sm leading-6 text-gray-600">سيصل رابط موافقة إلى البريد المسجل للعميل. عند الموافقة سيصله رابط منفصل ليختار كلمة مرور جديدة، وعند الرفض لن تتغير كلمة المرور.</span>
                    </span>
                </label>
                @error('form.requestPasswordChange')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('requestPasswordChange')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            @else
                <div class="flex items-start gap-4 rounded-xl border border-emerald-100 bg-emerald-50/70 p-4 sm:p-5">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 shadow-sm">
                        <i class="fas fa-envelope-open-text text-lg" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900">العميل هيحدد كلمة المرور بنفسه</p>
                        <p class="mt-1 text-sm leading-6 text-gray-600">بعد إنشاء الحساب، هنرسل رسالة إلى البريد الإلكتروني المدخل تحتوي على زر «تعيين كلمة المرور». الرابط صالح لمدة 60 دقيقة ويُستخدم مرة واحدة.</p>
                    </div>
                </div>
            @endif
        </x-ui.card>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
            <x-ui.action
                href="{{ route('admin.users.index') }}"
                variant="secondary"
                wire:navigate
            >
                إلغاء والعودة للعملاء
            </x-ui.action>

            <x-ui.action
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">
                    {{ $isEdit ? 'حفظ التعديلات' : 'إنشاء العميل وإرسال رابط التعيين' }}
                </span>
                <span wire:loading wire:target="save">جارٍ الحفظ...</span>
            </x-ui.action>
        </div>
    </form>
</div>
