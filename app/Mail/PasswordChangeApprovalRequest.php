<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChangeApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $accountName,
        public string $requesterName,
        public string $approveUrl,
        public string $expiresAt,
    ) {
    }

    public function build(): static
    {
        return $this->subject('طلب تأكيد تغيير كلمة المرور')
            ->view('emails.password-change-approval');
    }
}
