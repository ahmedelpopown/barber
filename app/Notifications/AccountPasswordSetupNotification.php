<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountPasswordSetupNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token,
        public string $accountType,
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
            ->subject('أكمل إنشاء حسابك | باربر هاوس')
            ->view('emails.password-link', [
                'recipientName' => $notifiable->name ?? 'مرحبًا بك',
                'heading' => 'أكمل إعداد حسابك',
                'intro' => 'تم إنشاء حساب لك في باربر هاوس بواسطة إدارة الصالون. عيّن كلمة المرور الخاصة بك حتى تتمكن من تسجيل الدخول.',
                'buttonText' => 'إنشاء كلمة المرور',
                'actionUrl' => $url,
                'expiryText' => 'الرابط صالح لمدة 60 دقيقة ويُستخدم مرة واحدة.',
                'securityText' => 'إذا لم تكن تتوقع إنشاء هذا الحساب، تجاهل الرسالة ولا تشارك الرابط مع أي شخص.',
            ]);
    }
}
