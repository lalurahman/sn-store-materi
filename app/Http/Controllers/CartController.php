<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request, CartService $cartService): View
    {
        return view('pages.public.cart.index', [
            ...$cartService->getCartSummary($request->user()),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'searchQuery' => '',
        ]);
    }

    public function store(
        AddToCartRequest $request,
        Product $product,
        CartService $cartService
    ): RedirectResponse {
        $cartService->add($request->user(), $product, $request->validated('quantity') ?? 1);

        return back()->with('status', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(
        UpdateCartItemRequest $request,
        CartItem $cartItem,
        CartService $cartService
    ): RedirectResponse {
        $cartService->updateQuantity(
            $request->user(),
            $cartItem,
            $request->validated('quantity'),
        );

        return redirect()->route('cart.index')->with('status', 'Jumlah produk berhasil diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem, CartService $cartService): RedirectResponse
    {
        $cartService->remove($request->user(), $cartItem);

        return redirect()->route('cart.index')->with('status', 'Produk dihapus dari keranjang.');
    }
}
