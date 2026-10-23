<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

test('verified users can view the admin dashboard and recent registered users', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create([
        'name' => 'Admin Store',
        'email' => 'admin@sn-store.test',
    ]);
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('data-theme-toggle')
        ->assertSee('Dashboard')
        ->assertSee('Pengguna terdaftar')
        ->assertSee('Admin Store')
        ->assertSee('admin@sn-store.test')
        ->assertSee('Pratinjau produk tersedia di menu Produk')
        ->assertSee('Pratinjau transaksi tersedia di menu Transaksi');
});

test('guests are redirected to login when opening the admin dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('customers are redirected to the public page when opening the admin dashboard', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $customer = User::factory()->create();
    $customer->assignRole('customer');

    $this->actingAs($customer)
        ->get(route('dashboard'))
        ->assertRedirect(route('home'));
});
