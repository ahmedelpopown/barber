<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeesSeeder extends Seeder
{
    /**
     * Create stable local demo accounts. The same email is updated rather than duplicated.
     * Demo password for newly seeded accounts: 12345678.
     */
    public function run(): void
    {
    

        $accounts = [
            ['name' => 'مدير النظام', 'email' => 'admin@gmail.com', 'phone' => '01060000001', 'role' => 'admin', 'status' => 'active'],
            ['name' => 'مسؤول التشغيل', 'email' => 'employee@gmail.com', 'phone' => '01060000002', 'role' => 'employee', 'status' => 'active'],
            ['name' => 'محمد حسن', 'email' => 'barber1@gmail.com', 'phone' => '01060000003', 'role' => 'barber', 'status' => 'active'],
            ['name' => 'محمود علي', 'email' => 'barber2@gmail.com', 'phone' => '01060000004', 'role' => 'barber', 'status' => 'active'],
            ['name' => 'عمر إبراهيم', 'email' => 'barber3@gmail.com', 'phone' => '01060000005', 'role' => 'barber', 'status' => 'active'],
            ['name' => 'كريم مصطفى', 'email' => 'barber4@gmail.com', 'phone' => '01060000006', 'role' => 'barber', 'status' => 'active'],
            ['name' => 'حسام فتحي', 'email' => 'barber5@gmail.com', 'phone' => '01060000007', 'role' => 'barber', 'status' => 'inactive'],
        ];

        foreach ($accounts as $attributes) {
            $roleName = $attributes['role'];
            unset($attributes['role']);

            $employee = Employee::query()->updateOrCreate(
                ['email' => $attributes['email']],
                [
                    'name' => $attributes['name'],
                    'phone' => $attributes['phone'],
                    'status' => $attributes['status'],
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => now(),
                ]
            );

            $role = Role::findOrCreate($roleName, 'employee');
            $employee->syncRoles([$role]);
        }
    }
}
