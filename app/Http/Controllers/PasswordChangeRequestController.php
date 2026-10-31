<?php

namespace App\Http\Controllers;

use App\Mail\PasswordChangeRequestResult;
use App\Models\Employee;
use App\Models\PasswordChangeRequest as PasswordChangeRequestModel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordChangeRequestController extends Controller
{
    public function show(string $token): View
    {
        $changeRequest = $this->findRequest($token);
        $account = $changeRequest?->account();

        return view('web.auth.password-change-confirmation', [
            'valid' => $changeRequest !== null && $account !== null,
            'accountName' => $account?->name,
            'requesterName' => $changeRequest?->requested_by_name,
            'token' => $token,
        ]);
    }

    public function decide(Request $request, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
        ]);

        $result = DB::transaction(function () use ($token, $validated): array {
            $changeRequest = PasswordChangeRequestModel::query()
                ->where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->first();

            if (! $changeRequest || $changeRequest->status !== 'pending' || $changeRequest->expires_at->isPast()) {
                if ($changeRequest && $changeRequest->status === 'pending') {
                    $changeRequest->update([
                        'status' => 'expired',
                        'responded_at' => now(),
                    ]);
                }

                return [null, null];
            }

            $account = $changeRequest->account();

            if (! $account) {
                $changeRequest->update([
                    'status' => 'expired',
                    'responded_at' => now(),
                ]);

                return [null, null];
            }

            $changeRequest->update([
                'status' => $validated['decision'],
                'responded_at' => now(),
            ]);

            return [$changeRequest, $account];
        });

        [$changeRequest, $account] = $result;

        if (! $changeRequest || ! $account) {
            return redirect()->route('password-change.invalid');
        }

        if ($validated['decision'] === 'approved') {
            $broker = $changeRequest->account_type === 'employee' ? 'employees' : 'users';
            $status = Password::broker($broker)->sendResetLink(['email' => $account->email]);

            $decision = 'وافق صاحب الحساب على طلب تغيير كلمة المرور. أُرسلت إليه رسالة لإعداد كلمة مرور جديدة.';
            $message = $status === Password::RESET_LINK_SENT
                ? 'وافقت على الطلب. أرسلنا إليك رسالة منفصلة تحتوي على رابط آمن لتعيين كلمة مرور جديدة.'
                : 'تم تسجيل موافقتك. قد يكون رابط الإعداد قد أُرسل مؤخرًا؛ راجع بريدك، أو استخدم صفحة «نسيت كلمة المرور» بعد دقيقة.';
        } else {
            $decision = 'رفض صاحب الحساب طلب تغيير كلمة المرور. لم يتم تغيير كلمة المرور.';
            $message = 'تم تسجيل رفضك. لم يتم تغيير كلمة المرور، وتم إبلاغ مسؤول الطلب.';
        }

        $this->notifyRequester($changeRequest, $account, $decision);

        return redirect()->route('password-change.response')->with('response_message', $message);
    }

    public function response(): View
    {
        return view('web.auth.password-change-response', [
            'message' => session('response_message', 'تم استلام ردك.'),
        ]);
    }

    public function invalid(): View
    {
        return view('web.auth.password-change-response', [
            'message' => 'هذا الطلب غير صالح أو انتهت صلاحيته أو تم الرد عليه من قبل.',
        ]);
    }

    private function findRequest(string $token): ?PasswordChangeRequestModel
    {
        $changeRequest = PasswordChangeRequestModel::query()
            ->where('token_hash', hash('sha256', $token))
            ->where('status', 'pending')
            ->first();

        if (! $changeRequest) {
            return null;
        }

        if ($changeRequest->expires_at->isPast()) {
            $changeRequest->update([
                'status' => 'expired',
                'responded_at' => now(),
            ]);

            return null;
        }

        return $changeRequest;
    }

    private function notifyRequester(
        PasswordChangeRequestModel $changeRequest,
        User|Employee $account,
        string $decision
    ): void {
        if (blank($changeRequest->requested_by_email)) {
            return;
        }

        Mail::to($changeRequest->requested_by_email)->send(new PasswordChangeRequestResult(
            accountName: $account->name,
            decision: $decision,
            accountEmail: $account->email,
        ));
    }
}
