<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissionRegistrar = app(PermissionRegistrar::class);
        $permissionRegistrar->forgetCachedPermissions();

        $permissionNames = [
            'view dashboard',
            'view products',
            'create products',
            'update products',
            'delete products',
            'view categories',
            'create categories',
            'update categories',
            'delete categories',
            'view transactions',
            'update transactions',
            'view users',
            'create users',
            'update users',
            'delete users',
            'manage roles and permissions',
        ];

        foreach ($permissionNames as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $permissionRegistrar->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions($permissionNames);

        $customerRole = Role::firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);
        $customerRole->syncPermissions([
            'view products',
            'view categories',
        ]);

        $permissionRegistrar->forgetCachedPermissions();
    }
}
