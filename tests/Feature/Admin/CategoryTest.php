<?php

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

test('verified users can view categories stored in the database', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    Category::query()->create([
        'name' => 'Stationery',
        'slug' => 'stationery',
        'description' => 'Perlengkapan tulis dan kantor.',
    ]);
    Category::query()->create([
        'name' => 'Office',
        'slug' => 'office',
        'description' => null,
    ]);

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response->assertOk()
        ->assertSee('Daftar kategori produk')
        ->assertSee('2 kategori')
        ->assertSee('Office')
        ->assertSee('office')
        ->assertSee('Belum ada deskripsi')
        ->assertSee('Stationery')
        ->assertSee('stationery')
        ->assertSee('Perlengkapan tulis dan kantor.')
        ->assertSee('aria-label="Edit kategori Stationery"', escape: false)
        ->assertSee('aria-label="Hapus kategori Stationery"', escape: false)
        ->assertSee('data-confirm-delete', escape: false)
        ->assertSee('data-category-name="Stationery"', escape: false)
        ->assertDontSee('onsubmit="return confirm(', escape: false)
        ->assertDontSee('Smartphone');
});

test('verified users see the empty state when no categories are stored', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response->assertOk()
        ->assertSee('0 kategori')
        ->assertSee('Belum ada kategori')
        ->assertDontSee('Smartphone');
});

test('category descriptions are escaped when displayed in the admin list', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Category::query()->create([
        'name' => 'Script Test',
        'slug' => 'script-test',
        'description' => '<script>alert("xss")</script>',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.categories.index'));

    $response->assertOk()
        ->assertSee('<script>alert("xss")</script>', escape: true)
        ->assertDontSee('<script>alert("xss")</script>', escape: false);
});

test('verified users can view the category create form', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get(route('admin.categories.create'));

    $response->assertOk()
        ->assertSee('Tambah kategori')
        ->assertSee('name="name"', escape: false)
        ->assertSee('Slug akan dibuat otomatis dari nama kategori')
        ->assertDontSee('name="slug"', escape: false)
        ->assertSee('Simpan kategori')
        ->assertSee('action="'.route('admin.categories.store').'"', escape: false);
});

test('verified users can view a category edit form', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    Category::query()->create([
        'name' => 'Studio Audio',
        'slug' => 'studio-audio',
        'description' => 'Perangkat audio untuk kebutuhan studio.',
    ]);

    $response = $this->actingAs($user)->get(route('admin.categories.edit', 'studio-audio'));

    $response->assertOk()
        ->assertSee('Edit kategori')
        ->assertSee('value="Studio Audio"', escape: false)
        ->assertSee('Perbarui kategori')
        ->assertSee('action="'.route('admin.categories.update', 'studio-audio').'"', escape: false);
});

test('verified users can create a category', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)
        ->from(route('admin.categories.create'))
        ->post(route('admin.categories.store'), [
            'name' => 'Home Audio',
            'slug' => 'manual-value-should-be-ignored',
            'description' => 'Speaker dan perangkat audio rumah.',
        ]);

    $response->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('status', 'Kategori berhasil ditambahkan.');

    $this->assertDatabaseHas('categories', [
        'name' => 'Home Audio',
        'slug' => 'home-audio',
        'description' => 'Speaker dan perangkat audio rumah.',
    ]);
});

test('category create validation rejects a missing name', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)
        ->from(route('admin.categories.create'))
        ->followingRedirects()
        ->post(route('admin.categories.store'), [
            'name' => '',
            'description' => '',
        ]);

    $response->assertOk()
        ->assertSee('The name field is required.');

    $this->assertDatabaseCount('categories', 0);
});

test('categories with duplicate names receive a unique generated slug', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    $response = $this->actingAs($admin)
        ->from(route('admin.categories.create'))
        ->post(route('admin.categories.store'), [
            'name' => 'Audio',
            'description' => null,
        ]);

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'name' => 'Audio',
        'slug' => 'audio-2',
    ]);
});

test('verified users can update a category and regenerate its slug', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    $response = $this->actingAs($admin)
        ->put(route('admin.categories.update', $category->slug), [
            'name' => 'Audio Premium',
            'slug' => 'manual-value-should-be-ignored',
            'description' => 'Perangkat audio premium.',
        ]);

    $response->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('status', 'Kategori berhasil diperbarui.');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Audio Premium',
        'slug' => 'audio-premium',
        'description' => 'Perangkat audio premium.',
    ]);
});

test('updating other category fields does not duplicate its existing slug', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    $response = $this->actingAs($admin)
        ->put(route('admin.categories.update', $category->slug), [
            'name' => 'Audio',
            'description' => 'Perangkat audio.',
        ]);

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Perangkat audio.',
    ]);
});

test('category update regenerates a unique slug when its new name is already in use', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    Category::query()->create([
        'name' => 'Laptop',
        'slug' => 'laptop',
        'description' => null,
    ]);

    $response = $this->actingAs($admin)
        ->put(route('admin.categories.update', $category->slug), [
            'name' => 'Laptop',
            'description' => null,
        ]);

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Laptop',
        'slug' => 'laptop-2',
    ]);
});

test('verified users can delete a category', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('admin.categories.destroy', $category->slug));

    $response->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('status', 'Kategori berhasil dihapus.');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('guests are redirected to login when opening the category list', function () {
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect(route('login'));
});

test('guests cannot create categories', function () {
    $response = $this->post(route('admin.categories.store'), [
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);

    $response->assertRedirect(route('login'));

    $this->assertDatabaseCount('categories', 0);
});

test('customers are redirected to the public page when opening an admin page', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $customer = User::factory()->create();
    $customer->assignRole('customer');

    $this->actingAs($customer)
        ->get(route('admin.categories.index'))
        ->assertRedirect(route('home'));
});

test('customers cannot create categories', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $customer = User::factory()->create();
    $customer->assignRole('customer');

    $response = $this->actingAs($customer)
        ->post(route('admin.categories.store'), [
            'name' => 'Audio',
            'slug' => 'audio',
            'description' => null,
        ]);

    $response->assertRedirect(route('home'));

    $this->assertDatabaseCount('categories', 0);
});
