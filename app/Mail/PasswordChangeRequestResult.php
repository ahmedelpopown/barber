<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChangeRequestResult extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $accountName,
        public string $decision,
        public string $accountEmail,
    ) {
    }

    public function build(): static
    {
        return $this->subject('نتيجة طلب تغيير كلمة المرور')
            ->view('emails.password-change-request-result');
    }
}
