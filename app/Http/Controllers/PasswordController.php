<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function showForgotForm(Request $request): View
    {
        return view('web.auth.forgot-password', [
            'type' => $this->normalizeType($request->query('type')),
        ]);
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'type' => ['required', 'in:customer,employee'],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'أدخل بريدًا إلكترونيًا صحيحًا.',
            'email.max' => 'البريد الإلكتروني طويل جدًا.',
            'type.required' => 'نوع الحساب غير محدد.',
            'type.in' => 'نوع الحساب غير صالح.',
        ]);

        Password::broker($this->broker($validated['type']))
            ->sendResetLink(['email' => $validated['email']]);

        // Keep the response identical whether the account exists, is throttled, or not.
        return back()->with(
            'status',
            'إذا كان البريد مسجلًا لدينا، فسيصلك رابط إعادة تعيين كلمة المرور خلال دقائق. إذا طلبت رابطًا للتو، استخدم الرسالة السابقة.'
        );
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('web.auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
            'type' => $this->normalizeType($request->query('type')),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'type' => ['required', 'in:customer,employee'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'token.required' => 'رابط إعادة التعيين غير مكتمل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'type.required' => 'نوع الحساب غير محدد.',
            'type.in' => 'نوع الحساب غير صالح.',
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
        ]);

        $status = Password::broker($this->broker($validated['type']))->reset(
            [
                'email' => $validated['email'],
                'token' => $validated['token'],
                'password' => $validated['password'],
                'password_confirmation' => $request->input('password_confirmation'),
            ],
            function ($user, string $password): void {
                // Both account models use Laravel's hashed password cast.
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $loginRoute = $validated['type'] === 'employee' ? 'employee.login' : 'login';

            return redirect()->route($loginRoute)->with(
                'status',
                'تم تغيير كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.'
            );
        }

        return back()->withErrors([
            'email' => 'رابط إعادة التعيين غير صالح أو انتهت صلاحيته. اطلب رابطًا جديدًا.',
        ]);
    }

    private function broker(string $type): string
    {
        return $type === 'employee' ? 'employees' : 'users';
    }

    private function normalizeType(mixed $type): string
    {
        return $type === 'employee' ? 'employee' : 'customer';
    }
}
