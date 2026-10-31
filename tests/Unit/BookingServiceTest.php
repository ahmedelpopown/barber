<?php

use App\Models\Booking;
use App\Models\Employee;
use App\Models\User;
use App\Services\BookingService;

it('detects an employee booking conflict within ten minutes', function () {
    $employee = Employee::factory()->create();
    $user = User::factory()->create();
    $date = today()->toDateString();

    Booking::create([
        'user_id' => $user->id,
        'employee_id' => $employee->id,
        'date' => $date,
        'time' => '10:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);

    $conflict = app(BookingService::class)->hasConflict(
        $employee->id,
        $date,
        '10:05',
        User::factory()->create()->id
    );

    expect($conflict)->toBe('يوجد حجز آخر للموظف خلال أقل من 10 دقائق.');
});

it('cancels only pending bookings', function () {
    $booking = Booking::factory()->create(['status' => 'pending']);
    $bookingService = app(BookingService::class);

    expect($bookingService->cancel($booking))->toBeTrue()
        ->and($booking->fresh()->status)->toBe('cancelled')
        ->and($bookingService->cancel($booking->fresh()))->toBeFalse();
});

it('does not complete a booking before its start time', function () {
    $this->travelTo(\Carbon\Carbon::parse('2030-01-15 10:00:00'));

    try {
        $booking = Booking::factory()->create([
            'date' => today()->toDateString(),
            'time' => '11:00:00',
            'status' => 'pending',
        ]);

        $bookingService = app(BookingService::class);

        expect($bookingService->complete($booking))->toBeFalse()
            ->and($booking->fresh()->status)->toBe('pending');

        $this->travelTo(\Carbon\Carbon::parse('2030-01-15 11:00:00'));

        expect($bookingService->complete($booking->fresh()))->toBeTrue()
            ->and($booking->fresh()->status)->toBe('completed');
    } finally {
        $this->travelBack();
    }
});

it('updates booking details and synchronizes selected sub-services', function () {
    $customer = User::factory()->create();
    $employee = Employee::factory()->create();
    $service = Service::factory()->create();
    $oldSubService = $service->subServices()->create([
        'name' => 'الخدمة القديمة',
        'duration' => 20,
        'price' => '50.00',
    ]);
    $newSubService = $service->subServices()->create([
        'name' => 'الخدمة الجديدة',
        'duration' => 30,
        'price' => '75.00',
    ]);

    $booking = Booking::factory()->create([
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '09:00:00',
        'status' => 'pending',
    ]);
    $booking->services()->attach($oldSubService->id);

    $updated = app(BookingService::class)->update($booking, [
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '12:30:00',
        'service_ids' => [$newSubService->id],
    ]);

    expect($updated)->toBeTrue()
        ->and($booking->fresh()->time)->toBe('12:30:00')
        ->and($booking->fresh()->services->modelKeys())->toBe([$newSubService->id]);
});
