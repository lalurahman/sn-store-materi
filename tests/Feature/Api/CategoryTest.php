<?php

use App\Models\Category;

test('guests can retrieve categories with the public API fields', function () {
    $stationery = Category::query()->create([
        'name' => 'Stationery',
        'slug' => 'stationery',
        'description' => 'Perlengkapan tulis dan kantor.',
    ]);
    $office = Category::query()->create([
        'name' => 'Office',
        'slug' => 'office',
        'description' => null,
    ]);

    $response = $this->getJson(route('api.categories.index'));

    $response->assertOk()
        ->assertExactJson([
            'code' => 200,
            'status' => 'success',
            'message' => 'Daftar kategori berhasil diambil.',
            'data' => [
                [
                    'id' => $office->id,
                    'nama' => 'Office',
                    'slug' => 'office',
                    'deskripsi' => null,
                    'created_at' => $office->created_at->toJSON(),
                    'updated_at' => $office->updated_at->toJSON(),
                ],
                [
                    'id' => $stationery->id,
                    'nama' => 'Stationery',
                    'slug' => 'stationery',
                    'deskripsi' => 'Perlengkapan tulis dan kantor.',
                    'created_at' => $stationery->created_at->toJSON(),
                    'updated_at' => $stationery->updated_at->toJSON(),
                ],
            ],
        ]);
});
