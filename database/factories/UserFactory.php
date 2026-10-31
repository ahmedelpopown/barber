<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'أحمد محمد', 'محمد علي', 'محمود حسن', 'عمر إبراهيم',
                'يوسف أحمد', 'كريم مصطفى', 'مصطفى خالد', 'علي محمود',
                'زياد محمد', 'عبد الرحمن سمير', 'فارس أحمد', 'حسن فتحي',
            ]),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => null,
            'phone' => fake()->unique()->numerify('01#########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password123'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (): array => [
            'email_verified_at' => null,
        ]);
    }

    public function withoutPhone(): static
    {
        return $this->state(fn (): array => [
            'phone' => null,
        ]);
    }

    /** Assign the project's customer role after the customer is created. */
    public function customer(): static
    {
        return $this->afterCreating(function (User $user): void {
            $role = Role::findOrCreate('customer', 'web');
            $user->assignRole($role);
        });
    }
}
