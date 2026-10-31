<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'شراء كريم شعر',
                'شراء شفرات حلاقة',
                'فاتورة كهرباء',
                'صيانة كرسي الحلاقة',
                'فاتورة إنترنت',
                'مستلزمات تنظيف',
                'إيجار المحل',
            ]),
            'category' => fake()->randomElement(array_keys(Expense::CATEGORIES)),
            'amount' => fake()->randomFloat(2, 25, 12000),
            'expense_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'payment_method' => fake()->randomElement(array_keys(Expense::PAYMENT_METHODS)),
            'vendor' => fake()->optional()->company(),
            'receipt_reference' => fake()->optional()->bothify('INV-####'),
            'notes' => fake()->optional()->sentence(),
            'created_by_employee_id' => Employee::factory(),
        ];
    }

    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn (): array => [
            'created_by_employee_id' => $employee->getKey(),
        ]);
    }
}
