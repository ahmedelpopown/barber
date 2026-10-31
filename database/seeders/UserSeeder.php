<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Seed stable, repeatable demo customers. Safe to run more than once.
     */
    public function run(): void
    { 
        $customerRole = Role::findOrCreate('customer', 'web');

        $customers = [
            ['name' => 'أحمد محمد', 'email' => 'ahmed@gmail.com', 'phone' => '01070000001'],
            ['name' => 'محمد علي', 'email' => 'customer1@example.test', 'phone' => '01070000002'],
            ['name' => 'محمود حسن', 'email' => 'customer2@example.test', 'phone' => '01070000003'],
            ['name' => 'عمر إبراهيم', 'email' => 'customer3@example.test', 'phone' => '01070000004'],
            ['name' => 'يوسف أحمد', 'email' => 'customer4@example.test', 'phone' => '01070000005'],
            ['name' => 'كريم مصطفى', 'email' => 'customer5@example.test', 'phone' => '01070000006'],
            ['name' => 'مصطفى خالد', 'email' => 'customer6@example.test', 'phone' => '01070000007'],
            ['name' => 'علي محمود', 'email' => 'customer7@example.test', 'phone' => '01070000008'],
            ['name' => 'زياد محمد', 'email' => 'customer8@example.test', 'phone' => '01070000009'],
            ['name' => 'عبد الرحمن سمير', 'email' => 'customer9@example.test', 'phone' => '01070000010'],
            ['name' => 'فارس أحمد', 'email' => 'customer10@example.test', 'phone' => '01070000011'],
            ['name' => 'حسن فتحي', 'email' => 'customer11@example.test', 'phone' => '01070000012'],
        ];

        foreach ($customers as $attributes) {
            $user = User::query()->firstOrCreate(
                ['email' => $attributes['email']],
                [
                    'name' => $attributes['name'],
                    'phone' => $attributes['phone'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$customerRole]);
        }
    }
}
