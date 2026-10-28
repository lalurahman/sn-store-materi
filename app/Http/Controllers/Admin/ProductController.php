<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(ProductService $productService): View
    {
        return view('pages.admin.products.index', [
            'products' => $productService->getAllForIndex(),
        ]);
    }

    public function create(ProductService $productService): View
    {
        return view('pages.admin.products.create', [
            'categories' => $productService->getCategories(),
            'product' => null,
        ]);
    }

    public function store(StoreProductRequest $request, ProductService $productService): RedirectResponse
    {
        $productService->create($request->validated());

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product, ProductService $productService): View
    {
        $product = $productService->find($product->id);

        return view('pages.admin.products.edit', [
            'categories' => $productService->getCategories(),
            'imageUrl' => $productService->getImageUrl($product),
            'product' => $product,
        ]);
    }

    public function update(
        UpdateProductRequest $request,
        Product $product,
        ProductService $productService
    ): RedirectResponse {
        $productService->update($product, $request->validated());

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductService $productService): RedirectResponse
    {
        $productService->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil dihapus.');
    }
}
