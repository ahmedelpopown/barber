<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryUsage>
 */
class InventoryUsageFactory extends Factory
{
    protected $model = InventoryUsage::class;

    public function definition(): array
    {
        return [
            'inventory_item_id' => InventoryItem::factory(),
            'started_by_employee_id' => Employee::factory()->active(),
            'finished_by_employee_id' => null,
            'status' => InventoryUsage::STATUS_IN_USE,
            'started_at' => now()->subMinutes(fake()->numberBetween(5, 180)),
            'finished_at' => null,
        ];
    }

    public function inUse(): static
    {
        return $this->state(fn (): array => [
            'status' => InventoryUsage::STATUS_IN_USE,
            'finished_by_employee_id' => null,
            'finished_at' => null,
        ]);
    }

    public function consumed(?Employee $finishedBy = null): static
    {
        return $this->state(function () use ($finishedBy): array {
            $finishedAt = now()->subDays(fake()->numberBetween(0, 7));
            $startedAt = $finishedAt->copy()->subMinutes(fake()->numberBetween(5, 180));

            return [
                'finished_by_employee_id' => $finishedBy?->id ?? Employee::factory()->active(),
                'status' => InventoryUsage::STATUS_CONSUMED,
                'started_at' => $startedAt,
                'finished_at' => $finishedAt,
            ];
        });
    }
}
