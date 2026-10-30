<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk();
});

it('shows login links to guests in the public navigation', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Login')
        ->assertSee('href="'.route('login').'"', escape: false);
    $response->assertSee('Register')
        ->assertSee('href="'.route('register').'"', escape: false)
        ->assertDontSee('Logout');

    expect(substr_count($response->getContent(), 'href="'.route('login').'"'))->toBe(2);
    expect(substr_count($response->getContent(), 'href="'.route('register').'"'))->toBe(2);
});

it('shows dashboard links to authenticated users in the public navigation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('href="'.route('dashboard').'"', escape: false)
        ->assertSee('Logout')
        ->assertSee('action="'.route('logout').'"', escape: false)
        ->assertDontSee('Register')
        ->assertDontSee('href="'.route('login').'"', escape: false);

    expect(substr_count($response->getContent(), 'href="'.route('dashboard').'"'))->toBe(2);
    expect(substr_count($response->getContent(), 'action="'.route('logout').'"'))->toBe(2);
});

it('renders categories and the ten latest products from the database', function () {
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    foreach (range(1, 11) as $index) {
        $createdAt = now()->subDays($index);

        Product::query()->create([
            'category_id' => $category->id,
            'name' => match ($index) {
                1 => 'Newest Database Product',
                11 => 'Oldest Database Product',
                default => 'Database Product '.$index,
            },
            'sku' => 'SN-AUD-'.$index,
            'price' => 1299000,
            'stock' => 10,
            'image_path' => 'products/product-'.$index.'.png',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('data-theme-toggle')
        ->assertSee('Produk terbaru')
        ->assertSee('Audio')
        ->assertSee('href="'.route('products.index', ['category' => 'audio']).'"', escape: false)
        ->assertSee('Newest Database Product')
        ->assertDontSee('Oldest Database Product')
        ->assertSee('/storage/products/product-1.png');

    expect(substr_count($response->getContent(), 'data-product-card'))->toBe(10);
});

it('filters storefront products by category slug and search query', function () {
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
        'sku' => 'SN-AUD-SEARCH',
        'price' => 500000,
        'stock' => 5,
        'image_path' => 'products/speaker.png',
    ]);
    Product::query()->create([
        'category_id' => $audioCategory->id,
        'name' => 'Bluetooth Speaker',
        'sku' => 'SN-AUD-NOMATCH',
        'price' => 450000,
        'stock' => 5,
        'image_path' => 'products/bluetooth-speaker.png',
    ]);
    Product::query()->create([
        'category_id' => $accessoriesCategory->id,
        'name' => 'Wireless Mouse',
        'sku' => 'SN-ACC-SEARCH',
        'price' => 300000,
        'stock' => 5,
        'image_path' => 'products/mouse.png',
    ]);

    $response = $this->get(route('home', ['category' => 'audio', 'q' => 'Wireless']));

    $response->assertOk()
        ->assertSee('Kategori Audio')
        ->assertSee('Wireless Speaker')
        ->assertDontSee('Bluetooth Speaker')
        ->assertDontSee('Wireless Mouse');
});
