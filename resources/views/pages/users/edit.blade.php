<?php

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('تعديل عميل')] class extends Component
{
    public UserForm $form;

    public bool $isEdit = true;

    public function mount(User $user): void
    {
        $this->form->setUser($user);
    }

    public function save()
    {
        $passwordChangeRequested = $this->form->update();

        session()->flash(
            'message',
            $passwordChangeRequested
                ? 'تم تعديل بيانات العميل وإرسال طلب موافقة على تغيير كلمة المرور إلى بريده الإلكتروني.'
                : 'تم تعديل بيانات العميل بنجاح.'
        );

        return $this->redirect(route('admin.users.index'));
    }
};
?>

@include('pages.users.partials.form')
