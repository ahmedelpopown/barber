<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Employee;
use App\Models\SubService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Seed a small, repeatable set of past, today's, and upcoming bookings.
     * Times are spaced per barber to avoid creating obvious demo conflicts.
     */
    public function run(): void
    {
         

        $records = [
            ['customer' => 'customer1@example.test', 'barber' => 'barber1@gmail.com', 'days' => -2, 'time' => '11:00:00', 'status' => 'completed', 'service' => 'قص كلاسيك'],
            ['customer' => 'customer2@example.test', 'barber' => 'barber2@gmail.com', 'days' => -1, 'time' => '12:00:00', 'status' => 'completed', 'service' => 'تحديد لحية'],
            ['customer' => 'customer3@example.test', 'barber' => 'barber1@gmail.com', 'days' => 0, 'time' => '10:00:00', 'status' => null, 'service' => 'قص بالمقص'],
            ['customer' => 'customer4@example.test', 'barber' => 'barber1@gmail.com', 'days' => 0, 'time' => '12:00:00', 'status' => null, 'service' => 'قص ماكينة'],
            ['customer' => 'customer5@example.test', 'barber' => 'barber2@gmail.com', 'days' => 0, 'time' => '11:00:00', 'status' => null, 'service' => 'حلاقة لحية كاملة'],
            ['customer' => 'customer6@example.test', 'barber' => 'barber2@gmail.com', 'days' => 0, 'time' => '13:00:00', 'status' => null, 'service' => 'غسل وتجفيف الشعر'],
            ['customer' => 'customer7@example.test', 'barber' => 'barber1@gmail.com', 'days' => 1, 'time' => '10:00:00', 'status' => 'pending', 'service' => 'قص كلاسيك'],
            ['customer' => 'customer8@example.test', 'barber' => 'barber2@gmail.com', 'days' => 1, 'time' => '11:00:00', 'status' => 'pending', 'service' => 'تنظيف بشرة'],
            ['customer' => 'customer9@example.test', 'barber' => 'barber3@gmail.com', 'days' => 2, 'time' => '12:00:00', 'status' => 'pending', 'service' => 'صبغة شعر'],
            ['customer' => 'customer10@example.test', 'barber' => 'barber4@gmail.com', 'days' => 2, 'time' => '14:00:00', 'status' => 'cancelled', 'service' => 'قص شعر ولحية'],
        ];

        $todayTurn = 0;

        foreach ($records as $record) {
            $customer = User::query()->where('email', $record['customer'])->first();
            $employee = Employee::query()->where('email', $record['barber'])->first();
            $subService = SubService::query()->where('name', $record['service'])->first();

            if (! $customer || ! $employee || ! $subService) {
                continue;
            }

            $date = today()->addDays($record['days'])->toDateString();
            $scheduledAt = Carbon::parse($date.' '.$record['time']);
            $status = $record['status'];

            if ($status === null) {
                $status = $scheduledAt->isPast() ? 'completed' : 'pending';
            }

            $turn = 0;
            if ($date === today()->toDateString() && $status === 'pending') {
                $turn = ++$todayTurn;
            }

            $booking = Booking::query()->firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'time' => $record['time'],
                ],
                [
                    'status' => $status,
                    'turn' => $turn,
                ]
            );

            $booking->services()->syncWithoutDetaching([$subService->id]);
        }
    }
}
