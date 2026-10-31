<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /** Seed realistic inventory levels and a small usage history for local development. */
    public function run(): void
    {
       

        $items = [
            ['name' => 'كريم حلاقة', 'sku' => 'CREAM-SHAVE-001', 'description' => 'كريم حلاقة للاستخدام اليومي مع العملاء.', 'unit' => 'عبوة', 'unit_price' => '85.00', 'quantity' => 6, 'minimum_quantity' => 2, 'is_active' => true, 'current' => true, 'history' => 2],
            ['name' => 'واكس شعر', 'sku' => 'HAIR-WAX-001', 'description' => 'واكس لتصفيف الشعر بدرجة تثبيت متوسطة.', 'unit' => 'علبة', 'unit_price' => '120.00', 'quantity' => 4, 'minimum_quantity' => 2, 'is_active' => true, 'current' => false, 'history' => 1],
            ['name' => 'لوشن ما بعد الحلاقة', 'sku' => 'AFTER-SHAVE-001', 'description' => 'لوشن مهدئ للبشرة بعد الحلاقة.', 'unit' => 'زجاجة', 'unit_price' => '95.00', 'quantity' => 3, 'minimum_quantity' => 1, 'is_active' => true, 'current' => false, 'history' => 1],
            ['name' => 'شامبو شعر', 'sku' => 'HAIR-SHAMPOO-001', 'description' => 'شامبو للاستخدام داخل الصالون.', 'unit' => 'زجاجة', 'unit_price' => '150.00', 'quantity' => 2, 'minimum_quantity' => 2, 'is_active' => true, 'current' => false, 'history' => 0],
            ['name' => 'شرائط رقبة للاستعمال الواحد', 'sku' => 'NECK-STRIPS-001', 'description' => 'عبوة شرائط رقبة أحادية الاستخدام.', 'unit' => 'عبوة', 'unit_price' => '45.00', 'quantity' => 0, 'minimum_quantity' => 1, 'is_active' => true, 'current' => false, 'history' => 0],
            ['name' => 'بودرة تالْك', 'sku' => 'TALC-POWDER-001', 'description' => 'بودرة بعد الحلاقة.', 'unit' => 'عبوة', 'unit_price' => '55.00', 'quantity' => 8, 'minimum_quantity' => 2, 'is_active' => true, 'current' => false, 'history' => 0],
            ['name' => 'زيت لحية', 'sku' => 'BEARD-OIL-001', 'description' => 'زيت للعناية باللحية.', 'unit' => 'زجاجة', 'unit_price' => '180.00', 'quantity' => 1, 'minimum_quantity' => 2, 'is_active' => true, 'current' => false, 'history' => 0],
            ['name' => 'شفرات حلاقة', 'sku' => 'RAZOR-BLADES-001', 'description' => 'علبة شفرات احتياطية.', 'unit' => 'علبة', 'unit_price' => '70.00', 'quantity' => 15, 'minimum_quantity' => 4, 'is_active' => true, 'current' => false, 'history' => 0],
        ];

        $starterEmployee = Employee::query()->where('email', 'barber1@gmail.com')->first();
        $finisherEmployee = Employee::query()->where('email', 'employee@gmail.com')->first() ?? $starterEmployee;

        foreach ($items as $attributes) {
            $hasCurrentUsage = $attributes['current'];
            $historyCount = $attributes['history'];
            unset($attributes['current'], $attributes['history']);

            $item = InventoryItem::query()->firstOrCreate(
                ['sku' => $attributes['sku']],
                $attributes
            );

            // Do not add duplicate histories or reset live stock on every seeding run.
            if ($item->usages()->exists() || ! $starterEmployee) {
                continue;
            }

            for ($index = 0; $index < $historyCount; $index++) {
                $startedAt = now()->subDays(4 + ($index * 3))->setTime(10 + $index, 0);

                $item->usages()->create([
                    'started_by_employee_id' => $starterEmployee->id,
                    'finished_by_employee_id' => ($finisherEmployee ?? $starterEmployee)->id,
                    'status' => InventoryUsage::STATUS_CONSUMED,
                    'started_at' => $startedAt,
                    'finished_at' => $startedAt->copy()->addMinutes(50),
                ]);
            }

            if ($hasCurrentUsage && $item->quantity > 0) {
                $item->usages()->create([
                    'started_by_employee_id' => $starterEmployee->id,
                    'finished_by_employee_id' => null,
                    'status' => InventoryUsage::STATUS_IN_USE,
                    'started_at' => now()->subMinutes(35),
                    'finished_at' => null,
                ]);
            }
        }
    }
}
