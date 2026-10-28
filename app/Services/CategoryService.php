<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * @return Collection<int, Category>
     */
    public function getAll(): Collection
    {
        return Category::query()->orderBy('name')->get();
    }

    public function findBySlug(string $slug): Category
    {
        return Category::query()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * @param  array{name: string, description?: string|null}  $data
     */
    public function create(array $data): Category
    {
        return Category::query()->create([
            'name' => $data['name'],
            'slug' => $this->generateUniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * @param  array{name: string, description?: string|null}  $data
     */
    public function update(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'],
            'slug' => $this->generateUniqueSlug($data['name'], $category),
            'description' => $data['description'] ?? null,
        ]);

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    private function generateUniqueSlug(string $name, ?Category $category = null): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'category';
        }

        $baseSlug = Str::substr($baseSlug, 0, 120);
        $slug = $baseSlug;
        $suffix = 1;

        while (Category::query()
            ->where('slug', $slug)
            ->when($category, fn ($query) => $query->whereKeyNot($category->getKey()))
            ->exists()) {
            $suffix++;
            $suffixText = '-'.$suffix;
            $slug = Str::substr($baseSlug, 0, 120 - Str::length($suffixText)).$suffixText;
        }

        return $slug;
    }
}
