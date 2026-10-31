<?php

use App\Models\Booking;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Service;
use App\Models\SubService;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $this->withoutVite();

    foreach ([
        ['name' => 'admin', 'guard_name' => 'employee'],
        ['name' => 'employee', 'guard_name' => 'employee'],
        ['name' => 'barber', 'guard_name' => 'employee'],
        ['name' => 'customer', 'guard_name' => 'web'],
    ] as $role) {
        Role::firstOrCreate($role);
    }

    $this->admin = Employee::factory()->create();
    $this->admin->assignRole('admin');

    $this->employee = Employee::factory()->create();
    $this->employee->assignRole('employee');

    $this->customer = User::factory()->create();

    $this->service = Service::factory()->create();
    $this->subService = SubService::factory()->for($this->service)->create();

    $this->booking = Booking::factory()
        ->forDate(today()->toDateString())
        ->create([
            'user_id' => $this->customer->id,
            'employee_id' => $this->employee->id,
            'time' => '11:00:00',
            'status' => 'pending',
            'turn' => 1,
        ]);
    $this->booking->services()->attach($this->subService->id);

    $this->inventoryItem = InventoryItem::factory()->create();
    $this->expense = Expense::factory()->create([
        'created_by_employee_id' => $this->admin->id,
        'inventory_item_id' => null,
        'inventory_quantity' => null,
    ]);
});

it('renders every public and customer-facing page', function (): void {
    $urls = [
        route('home'),
        url('/up'),
        route('login'),
        route('register'),
        route('employee.login'),
    ];

    foreach ($urls as $url) {
        $response = $this->get($url);
        expect($response->getStatusCode())->toBe(200, "Public page did not render: {$url}");
    }

    // This page legitimately redirects without a pending registration email.
    $this->withSession(['email' => 'new-customer@example.com'])
        ->get(route('verification.notice'))
        ->assertOk();

    $this->actingAs($this->customer, 'customer');

    foreach ([
        route('customer.profile'),
        route('customer.profile.bookings'),
        route('customer.profile.current-booking'),
    ] as $url) {
        $response = $this->get($url);
        expect($response->getStatusCode())->toBe(200, "Customer page did not render: {$url}");
    }
});

it('renders every administrator page', function (): void {
    $this->actingAs($this->admin, 'employee');

    $urls = [
        route('admin.dashboard'),
        route('admin.employees.index'),
        route('admin.employees.create'),
        route('admin.employees.edit', $this->employee),
        route('admin.employees.show', $this->employee),
        route('admin.users.index'),
        route('admin.users.create'),
        route('admin.users.edit', $this->customer),
        route('admin.bookings.index'),
        route('admin.bookings.create'),
        route('admin.bookings.edit', $this->booking),
        route('admin.services.index'),
        route('admin.services.create'),
        route('admin.services.edit', $this->service),
        route('admin.resources.index'),
        route('admin.resources.create'),
        route('admin.resources.edit', $this->inventoryItem),
        route('admin.expenses.index'),
        route('admin.expenses.create'),
        route('admin.expenses.show', $this->expense),
        route('admin.expenses.edit', $this->expense),
    ];

    foreach ($urls as $url) {
        $response = $this->get($url);
        expect($response->getStatusCode())->toBe(200, "Administrator page did not render: {$url}");
    }
});

it('renders every employee-facing page for an assigned employee', function (): void {
    $this->actingAs($this->employee, 'employee');

    $urls = [
        route('employee.dashboard'),
        route('employee.resources.index'),
        route('employee.bookings.index'),
        route('employee.bookings.create'),
        route('employee.bookings.edit', $this->booking),
        route('employee.profile.show'),
    ];

    foreach ($urls as $url) {
        $response = $this->get($url);
        expect($response->getStatusCode())->toBe(200, "Employee page did not render: {$url}");
    }
});

it('shows real operational metrics on the administrator dashboard', function (): void {
    Booking::factory()->forDate(today()->toDateString())->create([
        'user_id' => $this->customer->id,
        'employee_id' => $this->employee->id,
        'status' => 'completed',
        'time' => '09:00:00',
    ]);

    Expense::factory()->create([
        'title' => 'اختبار مصروف الشهر',
        'amount' => 123.45,
        'expense_date' => now()->startOfMonth()->toDateString(),
        'created_by_employee_id' => $this->admin->id,
        'inventory_item_id' => null,
        'inventory_quantity' => null,
    ]);

    $this->actingAs($this->admin, 'employee')
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('لوحة التحكم')
        ->assertSee('حجوزات اليوم')
        ->assertSee('مصروفات الشهر الحالي')
        ->assertSee('اختبار مصروف الشهر');
});


it('includes a bounded fallback so the admin preloader cannot cover the page forever', function (): void {
    $this->actingAs($this->admin, 'employee');

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('app-preloader')
        ->assertSee('window.setTimeout(hidePreloader, 1600)', false)
        ->assertSee('preloader-timeout', false);
});
