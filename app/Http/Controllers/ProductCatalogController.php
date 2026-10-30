<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCatalogController extends Controller
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

        $products = Product::query()
            ->with('category')
            ->when($searchQuery !== '', function (Builder $query) use ($searchQuery): void {
                $query->where(function (Builder $query) use ($searchQuery): void {
                    $query->where('name', 'like', '%'.$searchQuery.'%')
                        ->orWhere('sku', 'like', '%'.$searchQuery.'%')
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
            ->paginate(12)
            ->withQueryString();

        return view('pages.public.products.index', [
            'categories' => $categories,
            'products' => $products,
            'searchQuery' => $searchQuery,
            'selectedCategory' => $selectedCategory,
            'selectedCategoryName' => $categories->firstWhere('slug', $selectedCategory)?->name,
        ]);
    }
}
