<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class SubServiceSeeder extends Seeder
{
    /** Seed realistic service options without creating detached or duplicate rows. */
    public function run(): void
    {
       

        $catalog = [
            'قص الشعر' => [
                ['name' => 'قص كلاسيك', 'duration' => 30, 'price' => '100.00'],
                ['name' => 'قص بالمقص', 'duration' => 35, 'price' => '120.00'],
                ['name' => 'قص ماكينة', 'duration' => 20, 'price' => '80.00'],
                ['name' => 'قص أطفال', 'duration' => 25, 'price' => '90.00'],
            ],
            'اللحية' => [
                ['name' => 'تحديد لحية', 'duration' => 15, 'price' => '50.00'],
                ['name' => 'حلاقة لحية كاملة', 'duration' => 25, 'price' => '75.00'],
                ['name' => 'تحديد لحية بالموس', 'duration' => 20, 'price' => '65.00'],
            ],
            'العناية بالشعر' => [
                ['name' => 'غسل وتجفيف الشعر', 'duration' => 20, 'price' => '60.00'],
                ['name' => 'ماسك شعر', 'duration' => 30, 'price' => '120.00'],
                ['name' => 'تصفيف الشعر', 'duration' => 20, 'price' => '70.00'],
            ],
            'العناية بالبشرة' => [
                ['name' => 'تنظيف بشرة', 'duration' => 45, 'price' => '220.00'],
                ['name' => 'ماسك وجه', 'duration' => 20, 'price' => '90.00'],
            ],
            'الصبغات' => [
                ['name' => 'صبغة شعر', 'duration' => 60, 'price' => '350.00'],
                ['name' => 'صبغة لحية', 'duration' => 30, 'price' => '150.00'],
            ],
            'الباقات' => [
                ['name' => 'قص شعر ولحية', 'duration' => 45, 'price' => '160.00'],
                ['name' => 'باقة العريس', 'duration' => 90, 'price' => '500.00'],
            ],
        ];

        foreach ($catalog as $serviceName => $subServices) {
            $service = Service::query()->firstOrCreate(['name' => $serviceName]);

            foreach ($subServices as $attributes) {
                $service->subServices()->firstOrCreate(
                    ['name' => $attributes['name']],
                    [
                        'duration' => $attributes['duration'],
                        // Current migration defines this column as string.
                        'price' => $attributes['price'],
                    ]
                );
            }
        }
    }
}
