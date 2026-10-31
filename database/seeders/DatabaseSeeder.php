<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Role definitions are safe and necessary in every environment.
        $this->call(RoleSeeder::class);

      

        $this->call([
            UserSeeder::class,
            EmployeesSeeder::class,
            ServiceSeeder::class,
            SubServiceSeeder::class,
            BookingSeeder::class,
            InventorySeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
