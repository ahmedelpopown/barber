<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ([
            ['name' => 'admin', 'guard_name' => 'employee'],
            ['name' => 'employee', 'guard_name' => 'employee'],
            ['name' => 'barber', 'guard_name' => 'employee'],
            ['name' => 'customer', 'guard_name' => 'web'],
        ] as $role) {
            Role::firstOrCreate($role);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
