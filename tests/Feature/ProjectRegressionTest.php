<?php

use App\Jobs\SendEmailVerificationOtp;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\EmailVerification;
use App\Models\Service;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'employee']);
    Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'employee']);
    Role::firstOrCreate(['name' => 'barber', 'guard_name' => 'employee']);
    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
});

it('renders employee status labels without referencing a booking variable', function () {
    $admin = Employee::factory()->create(['status' => 'active']);
    $admin->assignRole('admin');
    $employee = Employee::factory()->create(['status' => 'inactive']);

    $this->actingAs($admin, 'employee');

    Livewire::test('pages::employees.index')
        ->assertSee($employee->name)
        ->assertSee('لا يعمل');
});

it('renders employee dashboard with booking sub-services', function () {
    $employee = Employee::factory()->create(['status' => 'active']);
    $employee->assignRole('employee');
    $customer = User::factory()->create();

    $service = Service::query()->create(['name' => 'خدمات اختبارية']);
    $subService = $service->subServices()->create([
        'name' => 'قص شعر للتأكد',
        'duration' => 30,
        'price' => '120.00',
    ]);

    $booking = Booking::query()->create([
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => now()->subMinutes(5)->format('H:i:s'),
        'status' => 'pending',
        'turn' => 1,
    ]);
    $booking->services()->attach($subService->id);

    $this->actingAs($employee, 'employee');

    Livewire::test('pages::employees.dashboard')
        ->assertSee('قص شعر للتأكد');
});

it('creates a service and stores sub-service price and duration', function () {
    Livewire::test('pages::services.create')
        ->set('name', 'خدمة اختبار جديدة')
        ->set('subServices.0.name', 'خدمة فرعية اختبارية')
        ->set('subServices.0.duration', 25)
        ->set('subServices.0.price', '99.50')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.services.index'));

    $service = Service::query()->where('name', 'خدمة اختبار جديدة')->firstOrFail();

    $this->assertDatabaseHas('sub_services', [
        'service_id' => $service->id,
        'name' => 'خدمة فرعية اختبارية',
        'duration' => 25,
        'price' => '99.50',
    ]);
});

it('rejects editing a sub-service owned by another service', function () {
    $serviceA = Service::query()->create(['name' => 'الخدمة الأصلية']);
    $serviceA->subServices()->create([
        'name' => 'فرعي أصلي',
        'duration' => 20,
        'price' => '50.00',
    ]);

    $serviceB = Service::query()->create(['name' => 'خدمة أخرى']);
    $foreignSubService = $serviceB->subServices()->create([
        'name' => 'فرعي تابع لخدمة أخرى',
        'duration' => 30,
        'price' => '70.00',
    ]);

    Livewire::test('pages::services.edit', ['service' => $serviceA])
        ->set('subServices.0.id', $foreignSubService->id)
        ->call('update')
        ->assertHasErrors(['subServices.0.id']);

    expect($foreignSubService->fresh()->service_id)->toBe($serviceB->id);
});

it('rejects a new booking that conflicts with a nearby pending booking', function () {
    $employee = Employee::factory()->create(['status' => 'active']);
    $customer = User::factory()->create();
    $otherCustomer = User::factory()->create();

    $service = Service::query()->create(['name' => 'خدمة حجز اختبار']);
    $subService = $service->subServices()->create([
        'name' => 'فرعي حجز اختبار',
        'duration' => 30,
        'price' => '100.00',
    ]);

    Booking::query()->create([
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '10:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);

    expect(fn () => app(BookingService::class)->create([
        'user_id' => $otherCustomer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '10:05',
        'service_ids' => [$subService->id],
    ]))->toThrow(ValidationException::class);

    expect(Booking::query()->count())->toBe(1);
});


it('keeps a customer and booking when deleting a customer with booking history', function () {
    $customer = User::factory()->create();
    $employee = Employee::factory()->create(['status' => 'active']);
    $booking = Booking::query()->create([
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '11:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);

    Livewire::test('pages::users.index')
        ->call('confirmDelete', $customer->id)
        ->call('deleteUser');

    $this->assertModelExists($customer);
    $this->assertModelExists($booking);
});

it('keeps an employee and booking when deleting an employee with booking history', function () {
    $admin = Employee::factory()->create(['status' => 'active']);
    $admin->assignRole('admin');
    $employee = Employee::factory()->create(['status' => 'active']);
    $employee->assignRole('employee');
    $customer = User::factory()->create();
    $booking = Booking::query()->create([
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '11:30:00',
        'status' => 'pending',
        'turn' => 1,
    ]);

    $this->actingAs($admin, 'employee');

    Livewire::test('pages::employees.index')
        ->call('confirmDelete', $employee->id)
        ->call('deleteEmployee');

    $this->assertModelExists($employee);
    $this->assertModelExists($booking);
});

it('keeps booked sub-services when a service edit tries to remove them', function () {
    $employee = Employee::factory()->create(['status' => 'active']);
    $customer = User::factory()->create();
    $service = Service::query()->create(['name' => 'خدمة لها سجل حجوزات']);

    $bookedSubService = $service->subServices()->create([
        'name' => 'خدمة فرعية مستخدمة',
        'duration' => 30,
        'price' => '100.00',
    ]);
    $retainedSubService = $service->subServices()->create([
        'name' => 'خدمة فرعية أخرى',
        'duration' => 20,
        'price' => '75.00',
    ]);

    $booking = Booking::query()->create([
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '12:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);
    $booking->services()->attach($bookedSubService->id);

    Livewire::test('pages::services.edit', ['service' => $service])
        ->set('subServices', [[
            'id' => $retainedSubService->id,
            'name' => $retainedSubService->name,
            'duration' => $retainedSubService->duration,
            'price' => $retainedSubService->price,
        ]])
        ->call('update')
        ->assertHasErrors(['subServices']);

    $this->assertModelExists($bookedSubService);
    $this->assertDatabaseHas('booking_sub_service', [
        'booking_id' => $booking->id,
        'sub_service_id' => $bookedSubService->id,
    ]);
});

it('renders the add-sub-service action through the shared card header slot', function () {
    Livewire::test('pages::services.create')
        ->assertSee('إضافة خدمة فرعية');
});


it('does not delete a service whose sub-services appear in booking history', function () {
    $employee = Employee::factory()->create(['status' => 'active']);
    $customer = User::factory()->create();
    $service = Service::query()->create(['name' => 'خدمة لا تحذف مع سجل الحجز']);
    $subService = $service->subServices()->create([
        'name' => 'خدمة فرعية محفوظة',
        'duration' => 30,
        'price' => '125.00',
    ]);
    $booking = Booking::query()->create([
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '12:30:00',
        'status' => 'pending',
        'turn' => 1,
    ]);
    $booking->services()->attach($subService->id);

    Livewire::test('pages::services.index')
        ->call('confirmDelete', $service->id)
        ->call('deleteService')
        ->assertSet('showDeleteModal', false);

    $this->assertModelExists($service);
    $this->assertModelExists($subService);
    $this->assertDatabaseHas('booking_sub_service', [
        'booking_id' => $booking->id,
        'sub_service_id' => $subService->id,
    ]);
});

it('rejects invalid service IDs instead of silently dropping them', function () {
    $employee = Employee::factory()->create(['status' => 'active']);
    $customer = User::factory()->create();
    $service = Service::query()->create(['name' => 'خدمة تحقق المعرفات']);
    $subService = $service->subServices()->create([
        'name' => 'خدمة فرعية صحيحة',
        'duration' => 20,
        'price' => '80.00',
    ]);

    expect(fn () => app(BookingService::class)->create([
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '13:00',
        'service_ids' => [$subService->id, 'not-an-id'],
    ]))->toThrow(ValidationException::class);

    $this->assertDatabaseCount('bookings', 0);
});


it('encrypts OTP values before queue serialization', function () {
    $plainOtp = '731942';
    $job = new SendEmailVerificationOtp('customer@example.test', $plainOtp);
    $serializedJob = serialize($job);

    expect($serializedJob)->not->toContain($plainOtp);
    expect(Crypt::decryptString(substr($job->otp, strlen('encrypted:'))))
        ->toBe($plainOtp);
});


it('persists sub-service price and duration when editing a service', function () {
    $service = Service::query()->create(['name' => 'خدمة تعديل السعر']);
    $subService = $service->subServices()->create([
        'name' => 'خدمة فرعية قبل التعديل',
        'duration' => 20,
        'price' => '50.00',
    ]);

    Livewire::test('pages::services.edit', ['service' => $service])
        ->set('subServices.0.price', '88.50')
        ->set('subServices.0.duration', 45)
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.services.index'));

    $this->assertDatabaseHas('sub_services', [
        'id' => $subService->id,
        'price' => '88.50',
        'duration' => 45,
    ]);
});

it('bulk-deletes only customers without booking history', function () {
    $customerWithBooking = User::factory()->create();
    $customerWithoutBooking = User::factory()->create();
    $employee = Employee::factory()->create(['status' => 'active']);

    $booking = Booking::query()->create([
        'user_id' => $customerWithBooking->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '14:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);

    Livewire::test('pages::users.index')
        ->set('selected', [$customerWithBooking->id, $customerWithoutBooking->id])
        ->call('bulkDelete');

    $this->assertModelExists($customerWithBooking);
    $this->assertModelExists($booking);
    $this->assertModelMissing($customerWithoutBooking);
});

it('bulk-deletes only employees without booking history', function () {
    $admin = Employee::factory()->create(['status' => 'active']);
    $admin->assignRole('admin');
    $employeeWithBooking = Employee::factory()->create(['status' => 'active']);
    $employeeWithoutBooking = Employee::factory()->create(['status' => 'active']);
    $customer = User::factory()->create();

    $booking = Booking::query()->create([
        'user_id' => $customer->id,
        'employee_id' => $employeeWithBooking->id,
        'date' => today()->toDateString(),
        'time' => '14:30:00',
        'status' => 'pending',
        'turn' => 1,
    ]);

    $this->actingAs($admin, 'employee');

    Livewire::test('pages::employees.index')
        ->set('selected', [$employeeWithBooking->id, $employeeWithoutBooking->id])
        ->call('bulkDelete');

    $this->assertModelExists($employeeWithBooking);
    $this->assertModelExists($booking);
    $this->assertModelMissing($employeeWithoutBooking);
});

it('bulk-deletes only services with no booked sub-services', function () {
    $protectedService = Service::query()->create(['name' => 'خدمة مرتبطة بحجز']);
    $deletableService = Service::query()->create(['name' => 'خدمة قابلة للحذف']);
    $protectedSubService = $protectedService->subServices()->create([
        'name' => 'فرعية مرتبطة بحجز',
        'duration' => 20,
        'price' => '40.00',
    ]);
    $employee = Employee::factory()->create(['status' => 'active']);
    $customer = User::factory()->create();
    $booking = Booking::query()->create([
        'user_id' => $customer->id,
        'employee_id' => $employee->id,
        'date' => today()->toDateString(),
        'time' => '15:00:00',
        'status' => 'pending',
        'turn' => 1,
    ]);
    $booking->services()->attach($protectedSubService->id);

    Livewire::test('pages::services.index')
        ->set('selected', [$protectedService->id, $deletableService->id])
        ->call('bulkDelete');

    $this->assertModelExists($protectedService);
    $this->assertModelExists($protectedSubService);
    $this->assertModelMissing($deletableService);
    $this->assertDatabaseHas('booking_sub_service', [
        'booking_id' => $booking->id,
        'sub_service_id' => $protectedSubService->id,
    ]);
});

it('allows an administrator to bulk-delete only visible bookings', function () {
    $admin = Employee::factory()->create(['status' => 'active']);
    $admin->assignRole('admin');
    $customer = User::factory()->create();

    $first = Booking::factory()->create([
        'user_id' => $customer->id,
        'employee_id' => $admin->id,
        'date' => today()->toDateString(),
        'time' => '16:00:00',
    ]);
    $second = Booking::factory()->create([
        'user_id' => $customer->id,
        'employee_id' => $admin->id,
        'date' => today()->toDateString(),
        'time' => '17:00:00',
    ]);

    $this->actingAs($admin, 'employee');

    Livewire::test('pages::bookings.index')
        ->set('selected', [$first->id, $second->id])
        ->call('bulkDelete');

    $this->assertModelMissing($first);
    $this->assertModelMissing($second);
});


it('creates employee accounts with a password hash and the operational barber role', function () {
    Livewire::test('pages::employees.create')
        ->set('form.name', 'موظف جديد للاختبار')
        ->set('form.email', 'new-barber@example.test')
        ->set('form.phone', '01012345678')
        ->set('form.password', 'secret-pass-123')
        ->set('form.status', 'active')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.employees.index'));

    $employee = Employee::query()->where('email', 'new-barber@example.test')->firstOrFail();

    expect($employee->hasRole('barber'))->toBeTrue();
    expect(Hash::check('secret-pass-123', $employee->password))->toBeTrue();
});

it('assigns the customer role when an administrator creates a customer', function () {
    Livewire::test('pages::users.create')
        ->set('form.name', 'عميل جديد للاختبار')
        ->set('form.email', 'new-customer@example.test')
        ->set('form.phone', '01098765432')
        ->set('form.password', 'secret-pass-123')
        ->set('form.password_confirmation', 'secret-pass-123')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $customer = User::query()->where('email', 'new-customer@example.test')->firstOrFail();

    expect($customer->hasRole('customer'))->toBeTrue();
    expect(Hash::check('secret-pass-123', $customer->password))->toBeTrue();
});


it('creates a pending email verification and queues an encrypted OTP instead of creating an unverified customer', function () {
    \Illuminate\Support\Facades\Queue::fake();

    $response = $this->post(route('register.submit'), [
        'name' => 'عميل تسجيل اختبار',
        'email' => 'otp-registration@example.test',
        'password' => 'secret-pass-123',
        'password_confirmation' => 'secret-pass-123',
    ]);

    $response->assertRedirect(route('verification.notice'));
    $this->assertDatabaseHas('email_verifications', [
        'email' => 'otp-registration@example.test',
        'name' => 'عميل تسجيل اختبار',
        'attempts' => 0,
    ]);
    $this->assertDatabaseMissing('users', ['email' => 'otp-registration@example.test']);

    \Illuminate\Support\Facades\Queue::assertPushed(SendEmailVerificationOtp::class, function (SendEmailVerificationOtp $job) {
        return $job->email === 'otp-registration@example.test'
            && str_starts_with($job->otp, 'encrypted:');
    });
});

it('creates and authenticates the customer only after a correct OTP', function () {
    $verification = EmailVerification::query()->create([
        'name' => 'عميل تم التحقق منه',
        'email' => 'otp-verified@example.test',
        'password' => Hash::make('secret-pass-123'),
        'otp' => Hash::make('123456'),
        'attempts' => 0,
        'expires_at' => now()->addMinutes(5),
        'last_sent_at' => now(),
    ]);

    $response = $this->withSession(['email' => $verification->email])
        ->post(route('verification.verify'), [
            'email' => $verification->email,
            'otp' => '123456',
        ]);

    $response->assertRedirect(route('home'));

    $customer = User::query()->where('email', $verification->email)->firstOrFail();
    expect($customer->hasRole('customer'))->toBeTrue();
    $this->assertAuthenticatedAs($customer, 'customer');
    $this->assertDatabaseMissing('email_verifications', ['email' => $verification->email]);
});

it('increments OTP attempts and refuses an incorrect code', function () {
    $verification = EmailVerification::query()->create([
        'name' => 'عميل رمز خاطئ',
        'email' => 'otp-wrong@example.test',
        'password' => Hash::make('secret-pass-123'),
        'otp' => Hash::make('123456'),
        'attempts' => 0,
        'expires_at' => now()->addMinutes(5),
        'last_sent_at' => now(),
    ]);

    $response = $this->withSession(['email' => $verification->email])
        ->post(route('verification.verify'), [
            'email' => $verification->email,
            'otp' => '654321',
        ]);

    $response->assertSessionHasErrors('otp');
    expect($verification->fresh()->attempts)->toBe(1);
    $this->assertDatabaseMissing('users', ['email' => $verification->email]);
});
