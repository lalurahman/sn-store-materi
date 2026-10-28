<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('verified users can create products and store their images on the public disk', function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    $response = $this->actingAs($admin)
        ->from(route('admin.products.create'))
        ->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Headphone Nirkabel',
            'sku' => 'SN-AUD-001',
            'price' => '899000',
            'stock' => 18,
            'image' => UploadedFile::fake()->image('headphone.png'),
        ]);

    $response->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('status', 'Produk berhasil ditambahkan.');

    $product = Product::query()->where('sku', 'SN-AUD-001')->firstOrFail();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'category_id' => $category->id,
        'name' => 'Headphone Nirkabel',
        'sku' => 'SN-AUD-001',
        'price' => '899000.00',
        'stock' => 18,
    ]);
    Storage::disk('public')->assertExists($product->image_path);
    expect($product->image_path)->toStartWith('products/');
});

test('verified users can view products with category, image, and stock status from the database', function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    foreach ([
        ['name' => 'Stok Habis', 'sku' => 'SKU-0', 'stock' => 0, 'path' => 'products/out.png', 'status' => 'Habis'],
        ['name' => 'Stok Menipis', 'sku' => 'SKU-10', 'stock' => 10, 'path' => 'products/low.png', 'status' => 'Stok menipis'],
        ['name' => 'Stok Tersedia', 'sku' => 'SKU-11', 'stock' => 11, 'path' => 'products/available.png', 'status' => 'Tersedia'],
    ] as $productData) {
        Storage::disk('public')->put($productData['path'], 'image');
        Product::query()->create([
            'category_id' => $category->id,
            'name' => $productData['name'],
            'sku' => $productData['sku'],
            'price' => 500000,
            'stock' => $productData['stock'],
            'image_path' => $productData['path'],
        ]);
    }

    $response = $this->actingAs($admin)->get(route('admin.products.index'));

    $response->assertOk()
        ->assertSee('3 produk')
        ->assertSee('Stok Habis')
        ->assertSee('Stok Menipis')
        ->assertSee('Stok Tersedia')
        ->assertSee('Audio')
        ->assertSee('Habis')
        ->assertSee('Stok menipis')
        ->assertSee('Tersedia')
        ->assertSee('/storage/products/out.png')
        ->assertSee('aria-label="Edit produk Stok Habis"', escape: false)
        ->assertSee('data-confirm-title="Hapus produk?"', escape: false);
});

test('verified users can view the product create form with categories and an image input', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.products.create'));

    $response->assertOk()
        ->assertSee('Tambah produk')
        ->assertSee('name="category_id"', escape: false)
        ->assertSee('Audio')
        ->assertSee('name="image"', escape: false)
        ->assertSee('enctype="multipart/form-data"', escape: false)
        ->assertSee('Status stok dibuat otomatis');
});

test('verified users can view a product edit form and existing image', function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    Storage::disk('public')->put('products/headphone.png', 'image');
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Headphone',
        'sku' => 'SN-AUD-002',
        'price' => 500000,
        'stock' => 5,
        'image_path' => 'products/headphone.png',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.products.edit', $product));

    $response->assertOk()
        ->assertSee('Edit produk')
        ->assertSee('value="Headphone"', escape: false)
        ->assertSee('value="SN-AUD-002"', escape: false)
        ->assertSee('/storage/products/headphone.png')
        ->assertSee('name="image"', escape: false)
        ->assertSee('name="_method" value="PUT"', escape: false);
});

test('verified users can update product details and keep the existing image', function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    Storage::disk('public')->put('products/headphone.png', 'image');
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Headphone',
        'sku' => 'SN-AUD-003',
        'price' => 500000,
        'stock' => 18,
        'image_path' => 'products/headphone.png',
    ]);

    $response = $this->actingAs($admin)
        ->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Headphone Pro',
            'sku' => 'SN-AUD-003',
            'price' => '750000',
            'stock' => 8,
        ]);

    $response->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('status', 'Produk berhasil diperbarui.');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Headphone Pro',
        'price' => '750000.00',
        'stock' => 8,
        'image_path' => 'products/headphone.png',
    ]);
    Storage::disk('public')->assertExists('products/headphone.png');
});

test('verified users can replace a product image and the old image is removed', function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    Storage::disk('public')->put('products/old.png', 'old image');
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Headphone',
        'sku' => 'SN-AUD-004',
        'price' => 500000,
        'stock' => 10,
        'image_path' => 'products/old.png',
    ]);

    $response = $this->actingAs($admin)
        ->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Headphone',
            'sku' => 'SN-AUD-004',
            'price' => 500000,
            'stock' => 10,
            'image' => UploadedFile::fake()->image('new.png'),
        ]);

    $response->assertRedirect(route('admin.products.index'));

    $product->refresh();
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'image_path' => $product->image_path,
    ]);
    expect($product->image_path)->not->toBe('products/old.png');
    Storage::disk('public')->assertExists($product->image_path);
    Storage::disk('public')->assertMissing('products/old.png');
});

test('verified users can delete a product and its image', function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    Storage::disk('public')->put('products/headphone.png', 'image');
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Headphone',
        'sku' => 'SN-AUD-005',
        'price' => 500000,
        'stock' => 10,
        'image_path' => 'products/headphone.png',
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('admin.products.destroy', $product));

    $response->assertRedirect(route('admin.products.index'))
        ->assertSessionHas('status', 'Produk berhasil dihapus.');

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
    Storage::disk('public')->assertMissing('products/headphone.png');
});

test('product creation rejects invalid input without storing an image', function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)
        ->from(route('admin.products.create'))
        ->post(route('admin.products.store'), [
            'category_id' => 999,
            'name' => '',
            'sku' => '',
            'price' => '-1',
            'stock' => '-1',
            'image' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        ]);

    $response->assertRedirect(route('admin.products.create'))
        ->assertSessionHasErrors(['category_id', 'name', 'sku', 'price', 'stock', 'image']);

    $this->assertDatabaseCount('products', 0);
    expect(Storage::disk('public')->allFiles('products'))->toBe([]);
});

test('product creation rejects duplicate SKUs', function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Existing Headphone',
        'sku' => 'SN-AUD-DUP',
        'price' => 500000,
        'stock' => 5,
        'image_path' => 'products/existing.png',
    ]);

    $response = $this->actingAs($admin)
        ->from(route('admin.products.create'))
        ->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'New Headphone',
            'sku' => 'SN-AUD-DUP',
            'price' => 700000,
            'stock' => 12,
            'image' => UploadedFile::fake()->image('new.png'),
        ]);

    $response->assertRedirect(route('admin.products.create'))
        ->assertSessionHasErrors('sku');

    $this->assertDatabaseCount('products', 1);
    expect(Storage::disk('public')->allFiles('products'))->toBe([]);
});

test('guests cannot create products', function () {
    Storage::fake('public');

    $response = $this->post(route('admin.products.store'), [
        'name' => 'Headphone',
        'sku' => 'SN-AUD-006',
        'price' => 500000,
        'stock' => 10,
        'image' => UploadedFile::fake()->image('headphone.png'),
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('products', 0);
    expect(Storage::disk('public')->allFiles('products'))->toBe([]);
});

test('customers cannot create products', function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);

    $customer = User::factory()->create();
    $customer->assignRole('customer');

    $response = $this->actingAs($customer)
        ->post(route('admin.products.store'), [
            'name' => 'Headphone',
            'sku' => 'SN-AUD-007',
            'price' => 500000,
            'stock' => 10,
            'image' => UploadedFile::fake()->image('headphone.png'),
        ]);

    $response->assertRedirect(route('home'));
    $this->assertDatabaseCount('products', 0);
    expect(Storage::disk('public')->allFiles('products'))->toBe([]);
});
