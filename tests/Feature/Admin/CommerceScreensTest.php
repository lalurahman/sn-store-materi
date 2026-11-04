<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

test('verified users can view product, transaction, and user lists', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create([
        'name' => 'Admin Khusus',
        'email' => 'admin-khusus@example.test',
    ]);
    $user->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Headphone Nirkabel Sora',
        'sku' => 'SN-AUD-2401',
        'price' => 899000,
        'stock' => 18,
        'image_path' => 'products/headphone-sora.png',
    ]);
    $order = Order::query()->create([
        'order_number' => 'SN-ADMIN-ORDER',
        'user_id' => $user->id,
        'customer_name' => 'Raka Pratama',
        'customer_phone' => '+628123456789',
        'customer_email' => 'raka@example.test',
        'shipping_address' => 'Jl. Merdeka No. 1',
        'total' => 899000,
        'status' => 'pending_payment',
        'payment_status' => 'pending',
    ]);
    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 1,
        'unit_price' => $product->price,
        'line_total' => $product->price,
    ]);

    $pages = [
        [
            'route' => 'admin.products.index',
            'heading' => 'Daftar produk',
            'sample' => 'Headphone Nirkabel Sora',
            'indicator' => '1 produk',
        ],
        [
            'route' => 'admin.transactions.index',
            'heading' => 'Daftar transaksi',
            'sample' => 'SN-ADMIN-ORDER',
            'indicator' => '1 transaksi',
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

test('customers are redirected to the public page from admin module routes', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $customer = User::factory()->create();
    $customer->assignRole('customer');

    foreach (['admin.products.index', 'admin.transactions.index', 'admin.users.index'] as $routeName) {
        $this->actingAs($customer)
            ->get(route($routeName))
            ->assertRedirect(route('home'));
    }
});

test('admins can view a transaction detail with items and shipping region', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $order = Order::query()->create([
        'order_number' => 'SN-DETAIL-001',
        'customer_name' => 'Rina Pembeli',
        'customer_phone' => '08123456789',
        'customer_email' => 'rina@example.test',
        'shipping_address' => 'Jl. Melati No. 3',
        'shipping_province_name' => 'NUSA TENGGARA BARAT',
        'shipping_city_name' => 'MATARAM',
        'shipping_postal_code' => '83121',
        'total' => 900000,
    ]);
    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_id' => null,
        'product_name' => 'Speaker Detail',
        'sku' => 'SN-DETAIL-SKU',
        'quantity' => 1,
        'unit_price' => 900000,
        'line_total' => 900000,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.transactions.show', 'SN-DETAIL-001'))
        ->assertOk()
        ->assertSee('Rina Pembeli')
        ->assertSee('Speaker Detail')
        ->assertSee('SN-DETAIL-SKU')
        ->assertSee('MATARAM')
        ->assertSee('83121')
        ->assertSee('Rp 900.000');

    $this->actingAs($admin)
        ->get(route('admin.transactions.show', 'SN-MISSING'))
        ->assertNotFound();
});

test('non-admin users cannot view a transaction detail', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $customer = User::factory()->create();
    $customer->assignRole('customer');

    $this->actingAs($customer)
        ->get(route('admin.transactions.show', 'SN-ANY'))
        ->assertRedirect();
});
