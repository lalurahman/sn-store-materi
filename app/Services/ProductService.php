<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProductService
{
    private const LOW_STOCK_THRESHOLD = 10;

    /**
     * @return array<int, array{id: int, name: string, sku: string, category: string, price: string, stock: int, status: string, image_url: string}>
     */
    public function getAllForIndex(): array
    {
        return Product::query()
            ->with('category')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'category' => $product->category->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'status' => $this->getStockStatus($product->stock),
                'image_url' => Storage::disk('public')->url($product->image_path),
            ])
            ->all();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function find(int $id): Product
    {
        return Product::query()
            ->with('category')
            ->findOrFail($id);
    }

    public function getImageUrl(Product $product): string
    {
        return Storage::disk('public')->url($product->image_path);
    }

    /**
     * @param  array{category_id: int, name: string, sku: string, price: numeric-string|int|float, stock: int, image: UploadedFile}  $data
     */
    public function create(array $data): Product
    {
        $imagePath = $this->storeImage($data['image']);

        try {
            return DB::transaction(fn (): Product => Product::query()->create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'sku' => $data['sku'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'image_path' => $imagePath,
            ]));
        } catch (Throwable $exception) {
            $this->deleteImage($imagePath);

            throw $exception;
        }
    }

    /**
     * @param  array{category_id: int, name: string, sku: string, price: numeric-string|int|float, stock: int, image?: UploadedFile|null}  $data
     */
    public function update(Product $product, array $data): Product
    {
        $previousImagePath = $product->image_path;
        $newImagePath = isset($data['image'])
            ? $this->storeImage($data['image'])
            : $previousImagePath;

        try {
            DB::transaction(fn () => $product->update([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'sku' => $data['sku'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'image_path' => $newImagePath,
            ]));
        } catch (Throwable $exception) {
            if ($newImagePath !== $previousImagePath) {
                $this->deleteImage($newImagePath);
            }

            throw $exception;
        }

        if ($newImagePath !== $previousImagePath) {
            $this->deleteImage($previousImagePath);
        }

        return $product;
    }

    public function delete(Product $product): void
    {
        $imagePath = $product->image_path;

        DB::transaction(fn () => $product->delete());

        $this->deleteImage($imagePath);
    }

    private function getStockStatus(int $stock): string
    {
        return match (true) {
            $stock === 0 => 'Habis',
            $stock <= self::LOW_STOCK_THRESHOLD => 'Stok menipis',
            default => 'Tersedia',
        };
    }

    private function storeImage(UploadedFile $image): string
    {
        $imagePath = $image->store('products', 'public');

        if (! is_string($imagePath) || $imagePath === '') {
            throw new RuntimeException('Gambar produk gagal disimpan ke local storage.');
        }

        return $imagePath;
    }

    private function deleteImage(string $imagePath): void
    {
        if ($imagePath !== '' && ! Storage::disk('public')->delete($imagePath)) {
            throw new RuntimeException('Gambar produk gagal dihapus dari local storage.');
        }
    }
}
