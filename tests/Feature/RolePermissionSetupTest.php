<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Spatie\Permission\Models\Role;

test('role and permission seeder creates baseline access mappings', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $adminRole = Role::findByName('admin');
    $customerRole = Role::findByName('customer');

    expect($adminRole->hasPermissionTo('manage roles and permissions'))->toBeTrue()
        ->and($adminRole->hasPermissionTo('delete users'))->toBeTrue()
        ->and($customerRole->hasPermissionTo('view products'))->toBeTrue()
        ->and($customerRole->hasPermissionTo('manage roles and permissions'))->toBeFalse();
});

test('role and permission seeder can run more than once', function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(RolesAndPermissionsSeeder::class);

    expect(Role::query()->where('name', 'admin')->count())->toBe(1)
        ->and(Role::query()->where('name', 'customer')->count())->toBe(1);
});

test('users can be assigned a role and inherit its permissions', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('customer');

    expect($user->hasRole('customer'))->toBeTrue()
        ->and($user->can('view products'))->toBeTrue()
        ->and($user->can('manage roles and permissions'))->toBeFalse();
});

test('admin user seeder creates the configured admin account with admin role', function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('email', 'admin@gmail.com')->firstOrFail();

    expect($admin->name)->toBe('Admin SN Store')
        ->and($admin->hasRole('admin'))->toBeTrue()
        ->and($admin->hasRole('customer'))->toBeFalse()
        ->and(password_verify('password123', $admin->password))->toBeTrue();
});
