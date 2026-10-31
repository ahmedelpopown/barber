<?php

use App\Models\Employee;
use App\Models\Expense;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin', 'employee');
    Role::findOrCreate('employee', 'employee');
});

function expenseAdmin(): Employee
{
    $admin = Employee::factory()->create(['status' => 'active']);
    $admin->assignRole('admin');

    return $admin;
}

test('expense pages require an admin employee', function (): void {
    $employee = Employee::factory()->create(['status' => 'active']);
    $employee->assignRole('employee');

    $this->actingAs($employee, 'employee')
        ->get(route('admin.expenses.index'))
        ->assertForbidden();
});

test('admin can create an expense', function (): void {
    $admin = expenseAdmin();

    Livewire::actingAs($admin, 'employee')
        ->test('pages::expenses.create')
        ->set('title', 'شراء كريمات')
        ->set('category', 'supplies')
        ->set('amount', '475.50')
        ->set('expense_date', now()->toDateString())
        ->set('payment_method', 'cash')
        ->set('vendor', 'مورد مستلزمات')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.expenses.index'));

    expect(Expense::query()->where('title', 'شراء كريمات')->first())
        ->not->toBeNull()
        ->and((float) Expense::query()->where('title', 'شراء كريمات')->value('amount'))
        ->toBe(475.5);
});

test('expense amount must be greater than zero', function (): void {
    $admin = expenseAdmin();

    Livewire::actingAs($admin, 'employee')
        ->test('pages::expenses.create')
        ->set('title', 'مصروف غير صحيح')
        ->set('category', 'supplies')
        ->set('amount', '0')
        ->set('expense_date', now()->toDateString())
        ->set('payment_method', 'cash')
        ->call('save')
        ->assertHasErrors(['amount']);

    expect(Expense::query()->where('title', 'مصروف غير صحيح')->exists())->toBeFalse();
});

test('deleted expenses are soft deleted and excluded from totals', function (): void {
    $admin = expenseAdmin();
    $expense = Expense::factory()->forEmployee($admin)->create([
        'amount' => 120,
        'expense_date' => now()->toDateString(),
    ]);

    Livewire::actingAs($admin, 'employee')
        ->test('pages::expenses.index')
        ->call('confirmDelete', $expense->id)
        ->call('deleteExpense')
        ->assertHasNoErrors();

    expect(Expense::withTrashed()->find($expense->id)->trashed())->toBeTrue()
        ->and((float) Expense::query()->sum('amount'))->toBe(0.0);
});
