<?php

use App\Models\User;

test('verified users can view the admin dashboard and recent registered users', function () {
    $user = User::factory()->create([
        'name' => 'Admin Store',
        'email' => 'admin@sn-store.test',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('data-theme-toggle')
        ->assertSee('Dashboard')
        ->assertSee('Pengguna terdaftar')
        ->assertSee('Admin Store')
        ->assertSee('admin@sn-store.test')
        ->assertSee('Modul produk belum tersedia');
});

test('guests are redirected to login when opening the admin dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});
