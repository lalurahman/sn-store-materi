<?php

use App\Models\User;

test('verified users can view category rows with edit and delete icons', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response->assertOk()
        ->assertSee('Daftar kategori produk')
        ->assertSee('Audio')
        ->assertSee('Smartphone')
        ->assertSee('Laptop')
        ->assertSee('Wearable')
        ->assertSee('Aksesori')
        ->assertSee('aria-label="Edit kategori Audio"', escape: false)
        ->assertSee('aria-label="Hapus kategori Audio (belum aktif)"', escape: false);
});

test('verified users can view the category create form without database persistence', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.categories.create'));

    $response->assertOk()
        ->assertSee('Tambah kategori')
        ->assertSee('Ini tampilan formulir. Penyimpanan kategori belum diaktifkan.')
        ->assertSee('name="name"', escape: false)
        ->assertSee('name="slug"', escape: false)
        ->assertSee('Simpan kategori');
});

test('verified users can view a category edit form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.categories.edit', 'audio'));

    $response->assertOk()
        ->assertSee('Edit kategori')
        ->assertSee('value="Audio"', escape: false)
        ->assertSee('Ini tampilan formulir. Penyimpanan kategori belum diaktifkan.');
});

test('guests are redirected to login when opening the category list', function () {
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect(route('login'));
});
