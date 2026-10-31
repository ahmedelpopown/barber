<?php

use App\Livewire\Forms\UserForm;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('إضافة عميل')] class extends Component
{
    public UserForm $form;

    public bool $isEdit = false;

    public function save()
    {
        $this->form->store();

        session()->flash('message', 'تم إنشاء حساب العميل وإرسال رابط تعيين كلمة المرور إلى بريده الإلكتروني.');

        return $this->redirect(route('admin.users.index'));
    }
};
?>

@include('pages.users.partials.form')
