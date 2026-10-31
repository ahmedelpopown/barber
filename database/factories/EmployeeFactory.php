<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'محمد حسن', 'محمود علي', 'عمر إبراهيم', 'كريم مصطفى',
                'حسام فتحي', 'أحمد سامي', 'مصطفى خالد', 'علي محمود',
            ]),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('01#########'),
            'password' => static::$password ??= Hash::make('password123'),
            'email_verified_at' => now(),
            'status' => 'active',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['status' => 'active']);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['status' => 'inactive']);
    }

    public function admin(): static
    {
        return $this->assignProjectRole('admin');
    }

    public function staff(): static
    {
        return $this->assignProjectRole('employee');
    }

    public function barber(): static
    {
        return $this->assignProjectRole('barber');
    }

    private function assignProjectRole(string $roleName): static
    {
        return $this->active()->afterCreating(function (Employee $employee) use ($roleName): void {
            $role = Role::findOrCreate($roleName, 'employee');
            $employee->assignRole($role);
        });
    }
}
