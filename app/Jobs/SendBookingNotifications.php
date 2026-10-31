<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Notifications\BookingCreatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBookingNotifications implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Retry count.
     */
    public int $tries = 3;

    /**
     * Retry delay.
     */
    public int $backoff = 10;

    public function __construct(
        public int $bookingId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $booking = Booking::query()
            ->with([
                'user',
                'employee',
                'services',
            ])
            ->find($this->bookingId);

        /*
        |--------------------------------------------------------------------------
        | Booking no longer exists
        |--------------------------------------------------------------------------
        */

        if (! $booking) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Customer notification
        |--------------------------------------------------------------------------
        */

        if ($booking->user) {
            $booking->user->notify(
                new BookingCreatedNotification($booking)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Employee notification
        |--------------------------------------------------------------------------
        */

        if ($booking->employee) {
            $booking->employee->notify(
                new BookingCreatedNotification($booking)
            );
        }
    }
}