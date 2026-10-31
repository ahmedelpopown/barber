<?php

namespace Database\Factories;

use App\Models\EmailVerification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<EmailVerification>
 */
class EmailVerificationFactory extends Factory
{
    protected $model = EmailVerification::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->randomElement([
                'أحمد محمد', 'محمد علي', 'محمود حسن', 'عمر إبراهيم',
                'يوسف أحمد', 'كريم مصطفى',
            ]),
            // The verification flow stores hashes, not plaintext passwords or OTPs.
            'password' => Hash::make('password123'),
            'otp' => Hash::make((string) fake()->numberBetween(100000, 999999)),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'last_sent_at' => now(),
            'verified_at' => null,
        ];
    }

    public function withOtp(string $otp): static
    {
        return $this->state(fn (): array => [
            'otp' => Hash::make($otp),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'expires_at' => now()->subMinute(),
            'verified_at' => null,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (): array => [
            'verified_at' => now(),
        ]);
    }

    public function withAttempts(int $attempts): static
    {
        return $this->state(fn (): array => [
            'attempts' => max(0, min(255, $attempts)),
        ]);
    }
}
