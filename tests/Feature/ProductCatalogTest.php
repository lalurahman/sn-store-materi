<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

it('shows database products with a category sidebar and asks guests to log in before adding products', function () {
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Wireless Speaker',
        'sku' => 'SN-AUD-CATALOG',
        'price' => 899000,
        'stock' => 12,
        'image_path' => 'products/speaker.png',
    ]);

    $response = $this->get(route('products.index'));

    $response->assertOk()
        ->assertSee('Filter kategori produk')
        ->assertSee('Semua kategori')
        ->assertSee('Audio')
        ->assertSee('Wireless Speaker')
        ->assertSee('/storage/products/speaker.png')
        ->assertSee('Login untuk menambahkan Wireless Speaker ke keranjang')
        ->assertSee('href="'.route('login').'"', escape: false);
});

it('filters catalog products by category and search query', function () {
    $audioCategory = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $accessoriesCategory = Category::query()->create([
        'name' => 'Aksesori',
        'slug' => 'aksesori',
        'description' => null,
    ]);

    Product::query()->create([
        'category_id' => $audioCategory->id,
        'name' => 'Wireless Speaker',
        'sku' => 'SN-AUD-WIRELESS',
        'price' => 899000,
        'stock' => 12,
        'image_path' => 'products/speaker.png',
    ]);
    Product::query()->create([
        'category_id' => $audioCategory->id,
        'name' => 'Bluetooth Headphones',
        'sku' => 'SN-AUD-BLUETOOTH',
        'price' => 1299000,
        'stock' => 8,
        'image_path' => 'products/headphones.png',
    ]);
    Product::query()->create([
        'category_id' => $accessoriesCategory->id,
        'name' => 'Wireless Mouse',
        'sku' => 'SN-ACC-WIRELESS',
        'price' => 429000,
        'stock' => 20,
        'image_path' => 'products/mouse.png',
    ]);

    $response = $this->get(route('products.index', ['category' => 'audio', 'q' => 'Wireless']));

    $response->assertOk()
        ->assertSee('Wireless Speaker')
        ->assertDontSee('Bluetooth Headphones')
        ->assertDontSee('Wireless Mouse')
        ->assertSee('name="category" value="audio"', escape: false)
        ->assertSee('name="q" value="Wireless"', escape: false);
});

it('shows signed-in customers an add-to-cart action only for in-stock products', function () {
    $user = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $availableProduct = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Available Speaker',
        'sku' => 'SN-AUD-AVAILABLE',
        'price' => 500000,
        'stock' => 2,
        'image_path' => 'products/available-speaker.png',
    ]);
    $unavailableProduct = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Sold Out Speaker',
        'sku' => 'SN-AUD-SOLD',
        'price' => 500000,
        'stock' => 0,
        'image_path' => 'products/sold-out-speaker.png',
    ]);

    $response = $this->actingAs($user)->get(route('products.index'));

    $response->assertOk()
        ->assertSee('action="'.route('cart.items.store', $availableProduct).'"', escape: false)
        ->assertSee('Tambah Available Speaker ke keranjang')
        ->assertSee('Stok Sold Out Speaker habis')
        ->assertDontSee('action="'.route('cart.items.store', $unavailableProduct).'"', escape: false);
});

it('links public product navigation to the catalog', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('href="'.route('products.index').'"', escape: false);
});
