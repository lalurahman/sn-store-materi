<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $searchQuery = $request->query('q');
        $searchQuery = is_string($searchQuery) ? trim($searchQuery) : '';

        $selectedCategory = $request->query('category');
        $selectedCategory = is_string($selectedCategory) ? trim($selectedCategory) : '';

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
        $selectedCategoryName = $categories->firstWhere('slug', $selectedCategory)?->name ?? $selectedCategory;

        $products = Product::query()
            ->with('category')
            ->when($searchQuery !== '', function (Builder $query) use ($searchQuery): void {
                $query->where(function (Builder $query) use ($searchQuery): void {
                    $query->where('name', 'like', '%'.$searchQuery.'%')
                        ->orWhereHas(
                            'category',
                            fn (Builder $categoryQuery): Builder => $categoryQuery
                                ->where('name', 'like', '%'.$searchQuery.'%'),
                        );
                });
            })
            ->when($selectedCategory !== '', function (Builder $query) use ($selectedCategory): void {
                $query->whereHas(
                    'category',
                    fn (Builder $categoryQuery): Builder => $categoryQuery->where('slug', $selectedCategory),
                );
            })
            ->latest()
            ->limit(10)
            ->get(['id', 'category_id', 'name', 'price', 'image_path'])
            ->map(fn (Product $product): array => [
                'name' => $product->name,
                'category' => $product->category->name,
                'price' => $product->price,
                'image' => Storage::disk('public')->url($product->image_path),
            ]);

        $slides = [
            [
                'title' => 'Teknologi terbaik untuk hidup yang lebih mudah',
                'description' => 'Temukan perangkat elektronik pilihan untuk bekerja, berkreasi, dan menikmati waktu.',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1400&q=90',
                'alt' => 'Headphone wireless berwarna merah',
            ],
            [
                'title' => 'Perangkat andal untuk ide yang lebih besar',
                'description' => 'Upgrade perangkat kerja dengan pilihan laptop dan aksesori yang mendukung aktivitasmu.',
                'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1400&q=90',
                'alt' => 'Laptop terbuka di atas meja kerja',
            ],
            [
                'title' => 'Abadikan setiap momen dengan detail',
                'description' => 'Pilih kamera dan perangkat kreatif untuk menangkap cerita dari sudut pandangmu.',
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1400&q=90',
                'alt' => 'Kamera digital untuk fotografi',
            ],
        ];

        return view('pages.public.home', [
            'categories' => $categories,
            'products' => $products,
            'searchQuery' => $searchQuery,
            'selectedCategory' => $selectedCategory,
            'selectedCategoryName' => $selectedCategoryName,
            'slides' => $slides,
        ]);
    }
}
