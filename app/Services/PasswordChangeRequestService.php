<?php

namespace App\Services;

use App\Mail\PasswordChangeApprovalRequest;
use App\Models\Employee;
use App\Models\PasswordChangeRequest as PasswordChangeRequestModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class PasswordChangeRequestService
{
    public function sendFor(User|Employee $account, string $accountType, Employee $requester): void
    {
        if (! in_array($accountType, ['customer', 'employee'], true)) {
            throw ValidationException::withMessages([
                'requestPasswordChange' => 'نوع الحساب غير صالح.',
            ]);
        }

        if (blank($account->email)) {
            throw ValidationException::withMessages([
                'requestPasswordChange' => 'لا يوجد بريد إلكتروني مسجل لهذا الحساب.',
            ]);
        }

        $plainToken = Str::random(64);

        $changeRequest = DB::transaction(function () use ($account, $accountType, $requester, $plainToken) {
            $recentRequestExists = PasswordChangeRequestModel::query()
                ->where('account_type', $accountType)
                ->where('account_id', $account->getKey())
                ->where('created_at', '>=', now()->subMinute())
                ->exists();

            if ($recentRequestExists) {
                throw ValidationException::withMessages([
                    'requestPasswordChange' => 'تم إرسال طلب تغيير كلمة المرور لهذا الحساب مؤخرًا. انتظر دقيقة قبل إرسال طلب جديد.',
                ]);
            }

            PasswordChangeRequestModel::query()
                ->where('account_type', $accountType)
                ->where('account_id', $account->getKey())
                ->where('status', 'pending')
                ->update([
                    'status' => 'expired',
                    'responded_at' => now(),
                ]);

            return PasswordChangeRequestModel::query()->create([
                'account_type' => $accountType,
                'account_id' => $account->getKey(),
                'requested_by_employee_id' => $requester->getKey(),
                'requested_by_name' => $requester->name,
                'requested_by_email' => $requester->email,
                'token_hash' => hash('sha256', $plainToken),
                'status' => 'pending',
                'expires_at' => now()->addDay(),
            ]);
        });

        $decisionUrl = route('password-change.confirm', ['token' => $plainToken]);

        try {
            Mail::to($account->email)->send(new PasswordChangeApprovalRequest(
                accountName: $account->name,
                requesterName: $requester->name,
                approveUrl: $decisionUrl,
                expiresAt: $changeRequest->expires_at->format('Y-m-d H:i'),
            ));
        } catch (Throwable $exception) {
            $changeRequest->delete();
            report($exception);

            throw ValidationException::withMessages([
                'requestPasswordChange' => 'تعذر إرسال رسالة الموافقة. راجع إعدادات البريد الإلكتروني وحاول مرة أخرى.',
            ]);
        }
    }
}
