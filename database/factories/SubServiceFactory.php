<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\SubService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubService>
 */
class SubServiceFactory extends Factory
{
    protected $model = SubService::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'name' => fake()->randomElement([
                'قص كلاسيك',
                'قص بالمقص',
                'قص ماكينة',
                'قص أطفال',
                'تحديد لحية',
                'حلاقة لحية كاملة',
                'غسل وتجفيف الشعر',
                'ماسك شعر',
                'تنظيف بشرة',
                'ماسك وجه',
                'صبغة شعر',
                'صبغة لحية',
            ]),
            // The current schema stores price as a string. Keep a decimal string here.
            'price' => number_format(fake()->randomFloat(2, 50, 650), 2, '.', ''),
            'duration' => fake()->randomElement([10, 15, 20, 25, 30, 45, 60, 90]),
        ];
    }

    public function pricedAt(float|int|string $price): static
    {
        return $this->state(fn (): array => [
            'price' => number_format((float) $price, 2, '.', ''),
        ]);
    }

    public function lasting(int $minutes): static
    {
        return $this->state(fn (): array => ['duration' => $minutes]);
    }
}
