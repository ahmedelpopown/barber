<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition(): array
    {
        $items = [
            ['name' => 'كريم حلاقة', 'unit' => 'عبوة', 'unit_price' => '85.00'],
            ['name' => 'واكس شعر', 'unit' => 'علبة', 'unit_price' => '120.00'],
            ['name' => 'لوشن ما بعد الحلاقة', 'unit' => 'زجاجة', 'unit_price' => '95.00'],
            ['name' => 'شامبو شعر', 'unit' => 'زجاجة', 'unit_price' => '150.00'],
            ['name' => 'بودرة تالْك', 'unit' => 'عبوة', 'unit_price' => '55.00'],
            ['name' => 'زيت لحية', 'unit' => 'زجاجة', 'unit_price' => '180.00'],
        ];

        $selected = fake()->randomElement($items);

        return [
            'name' => $selected['name'],
            'sku' => fake()->unique()->bothify('INV-####-????'),
            'description' => 'مورد للاستخدام اليومي داخل صالون الحلاقة.',
            'unit' => $selected['unit'],
            'unit_price' => $selected['unit_price'],
            'quantity' => fake()->numberBetween(2, 20),
            'minimum_quantity' => fake()->numberBetween(1, 4),
            'is_active' => true,
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (): array => [
            'quantity' => 2,
            'minimum_quantity' => 3,
            'is_active' => true,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (): array => [
            'quantity' => 0,
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }

    /** Create one active use record. Quantity includes the open unit. */
    public function inUse(?Employee $actor = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => true,
            'quantity' => max(1, (int) ($attributes['quantity'] ?? 1)),
        ])->afterCreating(function (InventoryItem $item) use ($actor): void {
            if ($item->quantity < 1) {
                $item->update(['quantity' => 1]);
            }

            if ($item->currentUsage()->exists()) {
                return;
            }

            $employee = $actor ?? Employee::factory()->active()->create();

            $item->usages()->create([
                'started_by_employee_id' => $employee->id,
                'finished_by_employee_id' => null,
                'status' => InventoryUsage::STATUS_IN_USE,
                'started_at' => now()->subMinutes(fake()->numberBetween(5, 180)),
                'finished_at' => null,
            ]);
        });
    }
}
