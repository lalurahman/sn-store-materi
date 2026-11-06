<?php

use App\Models\Category;
use App\Models\Product;

test('guests can retrieve products with their categories', function () {
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Perangkat audio.',
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Headphone Studio',
        'sku' => 'AUD-001',
        'price' => 1250000,
        'stock' => 8,
        'image_path' => 'products/headphone-studio.jpg',
    ]);

    $response = $this->getJson(route('api.products.index'));

    $response->assertOk()
        ->assertExactJson([
            'code' => 200,
            'status' => 'success',
            'message' => 'Daftar produk berhasil diambil.',
            'data' => [
                [
                    'id' => $product->id,
                    'name' => 'Headphone Studio',
                    'sku' => 'AUD-001',
                    'price' => '1250000.00',
                    'stock' => 8,
                    'image_url' => 'http://localhost:8000/storage/products/headphone-studio.jpg',
                    'category' => [
                        'id' => $category->id,
                        'nama' => 'Audio',
                        'slug' => 'audio',
                        'deskripsi' => 'Perangkat audio.',
                        'created_at' => $category->created_at->toJSON(),
                        'updated_at' => $category->updated_at->toJSON(),
                    ],
                    'created_at' => $product->created_at->toJSON(),
                    'updated_at' => $product->updated_at->toJSON(),
                ],
            ],
        ]);
});

test('guests can retrieve a product detail with its category', function () {
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Perangkat audio.',
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Headphone Studio',
        'sku' => 'AUD-001',
        'price' => 1250000,
        'stock' => 8,
        'image_path' => 'products/headphone-studio.jpg',
    ]);

    $response = $this->getJson(route('api.products.show', $product->id));

    $response->assertOk()
        ->assertExactJson([
            'code' => 200,
            'status' => 'success',
            'message' => 'Detail produk berhasil diambil.',
            'data' => [
                'id' => $product->id,
                'name' => 'Headphone Studio',
                'sku' => 'AUD-001',
                'price' => '1250000.00',
                'stock' => 8,
                'image_url' => 'http://localhost:8000/storage/products/headphone-studio.jpg',
                'category' => [
                    'id' => $category->id,
                    'nama' => 'Audio',
                    'slug' => 'audio',
                    'deskripsi' => 'Perangkat audio.',
                    'created_at' => $category->created_at->toJSON(),
                    'updated_at' => $category->updated_at->toJSON(),
                ],
                'created_at' => $product->created_at->toJSON(),
                'updated_at' => $product->updated_at->toJSON(),
            ],
        ]);
});

test('product detail returns 404 when the product does not exist', function () {
    $this->getJson(route('api.products.show', 999))
        ->assertNotFound();
});
