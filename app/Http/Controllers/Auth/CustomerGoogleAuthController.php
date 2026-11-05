<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class CustomerGoogleAuthController extends Controller
{
    /**
     * Redirect customer to Google.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->redirect();
    }

    /**
     * Handle Google callback.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect('/login')->with(
                'error',
                'تعذر تسجيل الدخول باستخدام Google. حاول مرة أخرى.'
            );
        }

        $googleId = (string) $googleUser->getId();

        $email = Str::lower(
            trim((string) $googleUser->getEmail())
        );

        $name = trim((string) $googleUser->getName());

        $avatar = $googleUser->getAvatar();

        // Google may expose this value under either key,
        // depending on the Socialite version.
        $emailVerified = filter_var(
            data_get(
                $googleUser->user,
                'email_verified',
                data_get($googleUser->user, 'verified_email', false)
            ),
            FILTER_VALIDATE_BOOLEAN
        );

        if (
            $googleId === ''
            || $email === ''
            || ! $emailVerified
        ) {
            return redirect('/login')->with(
                'error',
                'تعذر التحقق من حساب Google أو البريد الإلكتروني.'
            );
        }

        try {
            $result = DB::transaction(function () use (
                $googleId,
                $email,
                $name,
                $avatar,
            ) {
                // 1. Find account using Google's unique ID.
                $user = User::query()
                    ->where('google_id', $googleId)
                    ->lockForUpdate()
                    ->first();

                // 2. If not found, try linking an existing account by email.
                if (! $user) {
                    $user = User::query()
                        ->where('email', $email)
                        ->lockForUpdate()
                        ->first();
                }

                if ($user) {

                    // Prevent accounts with application roles
                    // from entering through customer Google authentication.
                    $hasCustomerRole = $user->hasRole('customer');

                    $hasOtherRoles = $user->roles()
                        ->where('name', '!=', 'customer')
                        ->exists();

                    if (! $hasCustomerRole || $hasOtherRoles) {
                        return [
                            'user' => null,
                            'error' => 'هذا الحساب غير مسموح له بتسجيل الدخول كعميل.',
                        ];
                    }

                    // Do not allow one Google account to replace another.
                    if (
                        $user->google_id
                        && ! hash_equals(
                            (string) $user->google_id,
                            $googleId
                        )
                    ) {
                        return [
                            'user' => null,
                            'error' => 'هذا البريد مرتبط بحساب Google آخر.',
                        ];
                    }

                    // Link Google to the existing customer account.
                    $user->google_id = $googleId;

                    // Google confirmed that this email is verified.
                    $user->email_verified_at ??= now();

                    // Preserve an existing uploaded avatar.
                    if (! $user->getRawOriginal('avatar') && $avatar) {
                        $user->avatar = $avatar;
                    }

                    $user->save();

                    return [
                        'user' => $user,
                        'error' => null,
                    ];
                }

                // 3. Create a new customer account.
                $user = new User();

                $user->name = $name ?: Str::before($email, '@');
                $user->email = $email;
                $user->password = Str::random(64);
                $user->google_id = $googleId;
                $user->email_verified_at = now();
                $user->avatar = $avatar;

                $user->save();
$user->assignRole('customer');
                return [
                    'user' => $user,
                    'error' => null,
                ];
            });
        } catch (Throwable $exception) {
            report($exception);

            return redirect('/login')->with(
                'error',
                'حدث خطأ أثناء إنشاء حسابك أو ربطه. حاول مرة أخرى.'
            );
        }

        if (! $result['user']) {
            return redirect('/login')->with(
                'error',
                $result['error']
            );
        }

        // Login using customer guard only.
        Auth::guard('customer')->login($result['user']);

        // Protect against session fixation.
        request()->session()->regenerate();

        return redirect()->intended('/');
    }
}
