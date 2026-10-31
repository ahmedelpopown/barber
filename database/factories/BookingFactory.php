<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use App\Models\SubService;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        // Keep the default appointment in the future, including after midnight.
        $startsAt = now()->addHours(2)->startOfMinute();

        return [
            'user_id' => User::factory(),
            'employee_id' => Employee::factory()->active(),
            'date' => $startsAt->toDateString(),
            'time' => $startsAt->format('H:i:s'),
            'status' => 'pending',
            'turn' => 0,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (): array => ['status' => 'pending']);
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => 'completed',
            'turn' => 0,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => [
            'status' => 'cancelled',
            'turn' => 0,
        ]);
    }

    public function forDate(string $date): static
    {
        return $this->state(fn (): array => ['date' => $date]);
    }

    public function at(string $date, string $time): static
    {
        return $this->state(fn (): array => [
            'date' => $date,
            'time' => $time,
        ]);
    }

    /** Attach one or more valid sub-services after the booking is created. */
    public function withServices(int $count = 1): static
    {
        $count = max(1, $count);

        return $this->afterCreating(function (Booking $booking) use ($count): void {
            $service = Service::factory()->create();
            $subServices = SubService::factory()
                ->count($count)
                ->for($service, 'service')
                ->create();

            $booking->services()->syncWithoutDetaching($subServices->modelKeys());
        });
    }
}
