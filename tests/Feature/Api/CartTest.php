<?php

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

function createApiCartProduct(int $stock = 5): Product
{
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Perangkat audio.',
    ]);

    return Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Headphone Studio',
        'sku' => 'AUD-001',
        'price' => 1250000,
        'stock' => $stock,
        'image_path' => 'products/headphone-studio.jpg',
    ]);
}

test('guests receive 401 when viewing or adding to a cart', function () {
    $product = createApiCartProduct();

    $this->getJson(route('api.cart.index'))
        ->assertUnauthorized();

    $this->postJson(route('api.cart.items.store', $product), ['quantity' => 1])
        ->assertUnauthorized();

    $this->assertDatabaseCount('cart_items', 0);
});

test('authenticated users can add products and retrieve their cart', function () {
    $user = User::factory()->create();
    $product = createApiCartProduct();

    $this->actingAs($user, 'sanctum')
        ->postJson(route('api.cart.items.store', $product), ['quantity' => 2])
        ->assertOk()
        ->assertJsonPath('code', 200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Produk berhasil ditambahkan ke keranjang.')
        ->assertJsonPath('data.item_count', 2)
        ->assertJsonPath('data.subtotal', '2500000.00')
        ->assertJsonPath('data.items.0.quantity', 2)
        ->assertJsonPath('data.items.0.product.id', $product->id);

    $this->actingAs($user, 'sanctum')
        ->postJson(route('api.cart.items.store', $product), ['quantity' => 1])
        ->assertOk()
        ->assertJsonPath('data.item_count', 3)
        ->assertJsonPath('data.items.0.quantity', 3);

    $this->actingAs($user, 'sanctum')
        ->getJson(route('api.cart.index'))
        ->assertOk()
        ->assertJsonPath('code', 200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Keranjang berhasil diambil.')
        ->assertJsonPath('data.item_count', 3)
        ->assertJsonPath('data.subtotal', '3750000.00')
        ->assertJsonPath('data.items.0.product.category.slug', 'audio');

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);
});

test('authenticated users cannot add quantities exceeding available stock', function () {
    $user = User::factory()->create();
    $product = createApiCartProduct(stock: 1);

    $this->actingAs($user, 'sanctum')
        ->postJson(route('api.cart.items.store', $product), ['quantity' => 2])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('quantity');

    $this->assertDatabaseCount('cart_items', 0);
});

test('authenticated users can only view their own cart', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = createApiCartProduct();
    CartItem::query()->create([
        'user_id' => $otherUser->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson(route('api.cart.index'))
        ->assertOk()
        ->assertJsonPath('data.item_count', 0)
        ->assertJsonPath('data.subtotal', '0.00')
        ->assertJsonPath('data.items', []);
});
