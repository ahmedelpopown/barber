<?php

use App\Livewire\Forms\EmployeeForm;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('إضافة موظف')] class extends Component {
    public EmployeeForm $form;

    public function save()
    {
        $this->form->store();

        session()->flash('message', 'تم إنشاء حساب الموظف وإرسال رابط تعيين كلمة المرور إلى بريده الإلكتروني.');

        return $this->redirect(route('admin.employees.index'));
    }
};
?>

<div dir="rtl" class="mx-auto w-full max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header
        title="إضافة موظف جديد"
        description="أدخل بيانات الموظف وحدد حالة عمله، وسنرسل إليه رابطًا آمنًا لتعيين كلمة المرور بنفسه."
    >
        <x-slot:actions>
            <x-ui.action variant="secondary" href="{{ route('admin.employees.index') }}">
                العودة للموظفين
            </x-ui.action>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.alert type="danger" :message="$errors->first() ?: session('error')" />

    <form wire:submit="save" class="space-y-6">
        <x-employees.form-fields />

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-start">
            <x-ui.action variant="secondary" href="{{ route('admin.employees.index') }}">
                إلغاء
            </x-ui.action>

            <x-ui.action
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">إنشاء الموظف وإرسال رابط التعيين</span>
                <span wire:loading wire:target="save">جارٍ الحفظ...</span>
            </x-ui.action>
        </div>
    </form>
</div>
