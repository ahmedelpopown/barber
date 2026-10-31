<?php

use App\Livewire\Forms\EmployeeForm;
use App\Models\Employee;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('تعديل موظف')] class extends Component {
    public EmployeeForm $form;

    public function mount(Employee $employee): void
    {
        $this->form->setEmployee($employee);
    }

    public function save()
    {
        $passwordChangeRequested = $this->form->update();

        session()->flash(
            'message',
            $passwordChangeRequested
                ? 'تم تعديل بيانات الموظف وإرسال طلب موافقة على تغيير كلمة المرور إلى بريده الإلكتروني.'
                : 'تم تعديل بيانات الموظف بنجاح.'
        );

        return $this->redirect(route('admin.employees.index'));
    }
};
?>

<div dir="rtl" class="mx-auto w-full max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
    <x-ui.page-header
        title="تعديل بيانات الموظف"
        description="عدّل البيانات المطلوبة، ويمكنك إرسال طلب تغيير كلمة المرور للموظف ليوافق عليه عبر البريد الإلكتروني."
    >
        <x-slot:actions>
            <x-ui.action variant="secondary" href="{{ route('admin.employees.index') }}">
                العودة للموظفين
            </x-ui.action>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.alert type="danger" :message="$errors->first() ?: session('error')" />

    <form wire:submit="save" class="space-y-6">
        <x-employees.form-fields :is-edit="true" />

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-start">
            <x-ui.action variant="secondary" href="{{ route('admin.employees.index') }}">
                إلغاء
            </x-ui.action>

            <x-ui.action
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">حفظ التعديلات</span>
                <span wire:loading wire:target="save">جارٍ الحفظ...</span>
            </x-ui.action>
        </div>
    </form>
</div>
