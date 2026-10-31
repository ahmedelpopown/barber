<?php

use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\SubService;
use App\Models\User;
use App\Services\BookingService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'employee', 'guard_name' => 'employee']);
    Role::create(['name' => 'admin', 'guard_name' => 'employee']);

    $this->employee = Employee::factory()->create();
    $this->employee->assignRole('employee');

    $this->otherEmployee = Employee::factory()->create();
    $this->otherEmployee->assignRole('employee');

    $this->customer = User::factory()->create();
});

it('does not let an employee cancel another employee booking', function () {
    $booking = Booking::create([
        'user_id' => $this->customer->id,
        'employee_id' => $this->otherEmployee->id,
        'date' => today()->toDateString(),
        'time' => '10:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);

    $this->actingAs($this->employee, 'employee');

    Livewire::test('pages::bookings.index')
        ->call('cancelled', $booking->id)
        ->assertStatus(404);

    expect($booking->fresh()->status)->toBe('pending');
});

it('does not let an employee bulk-delete another employee booking', function () {
    $booking = Booking::create([
        'user_id' => $this->customer->id,
        'employee_id' => $this->otherEmployee->id,
        'date' => today()->toDateString(),
        'time' => '10:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);

    $this->actingAs($this->employee, 'employee');

    Livewire::test('pages::bookings.index')
        ->set('selected', [$booking->id])
        ->call('bulkDelete');

    $this->assertModelExists($booking);
});

it('does not render another employee booking edit form', function () {
    $booking = Booking::create([
        'user_id' => $this->customer->id,
        'employee_id' => $this->otherEmployee->id,
        'date' => today()->toDateString(),
        'time' => '10:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);

    $this->actingAs($this->employee, 'employee')
        ->get(route('employee.bookings.edit', $booking))
        ->assertForbidden();
});

it('does not let an employee reassign their booking or change its customer', function () {
    $service = Service::factory()->create();
    $subService = SubService::factory()->for($service)->create();

    $booking = Booking::create([
        'user_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'date' => today()->toDateString(),
        'time' => '10:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);
    $booking->services()->attach($subService);

    $anotherCustomer = User::factory()->create();

    $this->actingAs($this->employee, 'employee');

    Livewire::test('pages::bookings.edit', ['booking' => $booking])
        ->set('form.user_id', $anotherCustomer->id)
        ->set('form.employee_id', $this->otherEmployee->id)
        ->call('save');

    expect($booking->fresh()->user_id)->toBe($this->customer->id)
        ->and($booking->fresh()->employee_id)->toBe($this->employee->id);
});

it('does not expose a customer password hash in the livewire edit page', function () {
    $admin = Employee::factory()->create();
    $admin->assignRole('admin');
    $customer = User::factory()->create();

    $this->actingAs($admin, 'employee')
        ->get(route('admin.users.edit', $customer))
        ->assertSuccessful()
        ->assertDontSee($customer->getRawOriginal('password'), false);
});

it('keeps a customer password unchanged when the edit password is blank', function () {
    $admin = Employee::factory()->create();
    $admin->assignRole('admin');
    $customer = User::factory()->create();
    $existingPasswordHash = $customer->getRawOriginal('password');

    $this->actingAs($admin, 'employee');

    Livewire::test('pages::users.edit', ['user' => $customer])
        ->assertSet('form.password', '')
        ->call('save');

    expect($customer->fresh()->getRawOriginal('password'))
        ->toBe($existingPasswordHash);
});

it('creates bookings using the selected service pivot records', function () {
    Queue::fake();

    $service = Service::factory()->create();
    $subService = SubService::factory()->for($service)->create();

    $booking = app(BookingService::class)->create([
        'user_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'date' => today()->toDateString(),
        'time' => '10:00',
        'service_ids' => [$subService->id],
    ]);

    expect($booking->services->modelKeys())->toBe([$subService->id]);
});

it('does not seed demo accounts outside local and testing environments', function () {
    $userCount = User::query()->count();
    $employeeCount = Employee::query()->count();
    $this->app['env'] = 'production';

    app(DatabaseSeeder::class)->run();

    expect(User::query()->count())->toBe($userCount)
        ->and(Employee::query()->count())->toBe($employeeCount)
        ->and(Role::query()->where('name', 'admin')->exists())->toBeTrue();
});
