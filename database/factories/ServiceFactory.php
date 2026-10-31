<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'قص الشعر',
                'اللحية',
                'العناية بالشعر',
                'العناية بالبشرة',
                'الصبغات',
                'الباقات',
                'تصفيف الشعر',
                'العناية بالعريس',
            ]),
        ];
    }

    public function named(string $name): static
    {
        return $this->state(fn (): array => ['name' => $name]);
    }
}
