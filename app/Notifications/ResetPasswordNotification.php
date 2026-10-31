<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token,
        public string $accountType = 'customer',
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
            'type' => $this->accountType,
        ]);

        return (new MailMessage())
            ->subject('إعادة تعيين كلمة المرور | باربر هاوس')
            ->view('emails.password-link', [
                'recipientName' => $notifiable->name ?? 'عميلنا العزيز',
                'heading' => 'إعادة تعيين كلمة المرور',
                'intro' => 'وصلنا طلب لإعادة تعيين كلمة المرور الخاصة بحسابك في باربر هاوس.',
                'buttonText' => 'تعيين كلمة مرور جديدة',
                'actionUrl' => $url,
                'expiryText' => 'الرابط صالح لمدة 60 دقيقة ويُستخدم مرة واحدة.',
                'securityText' => 'إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة. لن تتغير كلمة مرورك ما لم تفتح الرابط وتكمل الخطوات.',
            ]);
    }
}
