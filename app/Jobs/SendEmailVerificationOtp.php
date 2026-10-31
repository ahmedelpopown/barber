<?php

namespace App\Jobs;

use App\Mail\VerifyEmailOtp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class SendEmailVerificationOtp implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public string $email;

    /** Encrypted before serialization so OTPs are not stored as plaintext in the queue. */
    public string $otp;

    public function __construct(string $email, string $otp)
    {
        $this->email = $email;
        $this->otp = 'encrypted:' . Crypt::encryptString($otp);
    }

    public function handle(): void
    {
        // Support already-queued jobs created by the previous version during deployment.
        $otp = str_starts_with($this->otp, 'encrypted:')
            ? Crypt::decryptString(substr($this->otp, strlen('encrypted:')))
            : $this->otp;

        Mail::to($this->email)->send(new VerifyEmailOtp($otp));
    }
}
