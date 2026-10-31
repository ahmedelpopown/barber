<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\AccountPasswordSetupNotification;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Throwable;

class AccountPasswordSetupService
{
    public function send(User|Employee $account, string $accountType): void
    {
        if (! in_array($accountType, ['customer', 'employee'], true)) {
            throw ValidationException::withMessages([
                'email' => 'نوع الحساب غير صالح لإرسال رابط تعيين كلمة المرور.',
            ]);
        }

        $broker = Password::broker($accountType === 'employee' ? 'employees' : 'users');
        $token = $broker->createToken($account);

        try {
            $account->notify(new AccountPasswordSetupNotification($token, $accountType));
        } catch (Throwable $exception) {
            $broker->deleteToken($account);
            report($exception);

            throw ValidationException::withMessages([
                'email' => 'تعذر إرسال رابط تعيين كلمة المرور. تأكد من إعدادات البريد الإلكتروني ثم حاول مرة أخرى.',
            ]);
        }
    }
}
