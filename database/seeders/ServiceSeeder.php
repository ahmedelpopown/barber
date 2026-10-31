<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /** Create the main service categories only; SubServiceSeeder adds their options. */
    public function run(): void
    {
         

        foreach ([
            'قص الشعر',
            'اللحية',
            'العناية بالشعر',
            'العناية بالبشرة',
            'الصبغات',
            'الباقات',
        ] as $name) {
            Service::query()->firstOrCreate(['name' => $name]);
        }
    }
}
