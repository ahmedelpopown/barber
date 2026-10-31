<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public Booking $booking
    ) {
    }

    /**
     * Notification channels.
     */
    public function via(object $notifiable): array
    {
        return [
            'database',
            'mail',
        ];
    }

    /**
     * Database notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,

            'title' => $this->isEmployee($notifiable)
                ? 'حجز جديد'
                : 'تم تأكيد الحجز',

            'message' => $this->buildMessage($notifiable),

            'date' => $this->booking->date,

            'time' => $this->booking->time,

            'employee_id' => $this->booking->employee_id,

            'user_id' => $this->booking->user_id,
        ];
    }

    /**
     * Email notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isEmployee = $this->isEmployee($notifiable);

        return (new MailMessage)
            ->subject($isEmployee
                ? 'حجز جديد - ' . $this->booking->date . ' | باربر هاوس'
                : 'تم تأكيد حجزك | باربر هاوس')
            ->view('emails.booking-created', [
                'recipientName' => $notifiable->name ?? 'عميلنا العزيز',
                'heading' => $isEmployee ? 'لديك حجز جديد' : 'تم تأكيد حجزك',
                'intro' => $isEmployee
                    ? 'تم إنشاء حجز جديد على حسابك. راجع تفاصيل الموعد من خلال لوحة التحكم.'
                    : 'تم إنشاء حجزك بنجاح في باربر هاوس. ننتظرك في الموعد المحدد.',
                'bookingMessage' => $this->buildMessage($notifiable),
                'bookingDate' => $this->booking->date,
                'bookingTime' => $this->booking->time,
                'otherPartyLabel' => $isEmployee ? 'العميل' : 'الحلاق',
                'otherPartyName' => $isEmployee
                    ? ($this->booking->user?->name ?? 'غير محدد')
                    : ($this->booking->employee?->name ?? 'غير محدد'),
                'actionUrl' => $this->bookingUrl($notifiable),
                'actionText' => 'عرض تفاصيل الحجز',
                'footerText' => $isEmployee
                    ? 'يرجى مراجعة تفاصيل الحجز قبل الموعد.'
                    : 'نتمنى لك تجربة ممتازة في باربر هاوس.',
            ]);
    }

    /**
     * Build notification message.
     */
    protected function buildMessage(object $notifiable): string
    {
        if ($this->isEmployee($notifiable)) {
            return sprintf(
                'لديك حجز جديد مع العميل %s الساعة %s.',
                $this->booking->user?->name ?? 'العميل',
                $this->booking->time
            );
        }

        return sprintf(
            'تم إنشاء حجزك بنجاح مع الحلاق %s الساعة %s.',
            $this->booking->employee?->name ?? 'الحلاق',
            $this->booking->time
        );
    }

    /**
     * Check if the notification belongs to an employee.
     */
    protected function isEmployee(object $notifiable): bool
    {
        return $notifiable instanceof Employee;
    }

    /**
     * Link to the recipient's booking list.
     */
    protected function bookingUrl(object $notifiable): string
    {
        if ($notifiable instanceof Employee) {
            return $notifiable->hasRole('admin')
                ? route('admin.bookings.index')
                : route('employee.bookings.index');
        }

        return route('customer.profile.bookings');
    }
}