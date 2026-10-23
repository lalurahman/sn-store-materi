<?php

use App\Models\User;

test('verified users can view dummy product, transaction, and user lists', function () {
    $user = User::factory()->create([
        'name' => 'Admin Khusus',
        'email' => 'admin-khusus@example.test',
    ]);

    $pages = [
        [
            'route' => 'admin.products.index',
            'heading' => 'Daftar produk',
            'sample' => 'Headphone Nirkabel Sora',
            'indicator' => 'produk contoh',
        ],
        [
            'route' => 'admin.transactions.index',
            'heading' => 'Daftar transaksi',
            'sample' => 'INV-2026-1042',
            'indicator' => 'transaksi contoh',
        ],
        [
            'route' => 'admin.users.index',
            'heading' => 'Daftar user',
            'sample' => 'Raka Pratama',
            'indicator' => 'user contoh',
        ],
    ];

    foreach ($pages as $page) {
        $this->actingAs($user)
            ->get(route($page['route']))
            ->assertOk()
            ->assertSee($page['heading'])
            ->assertSee($page['sample'])
            ->assertSee($page['indicator']);
    }

    $this->get(route('admin.users.index'))
        ->assertDontSee('admin-khusus@example.test');
});

test('guests are redirected to login when opening dummy admin lists', function () {
    foreach (['admin.products.index', 'admin.transactions.index', 'admin.users.index'] as $routeName) {
        $this->get(route($routeName))
            ->assertRedirect(route('login'));
    }
});
