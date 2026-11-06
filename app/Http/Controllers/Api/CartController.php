<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\CartItemResource;
use App\Models\Product;
use App\Services\CartService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function index(CartService $cartService): JsonResponse
    {
        $summary = $cartService->getCartSummary(request()->user());

        return ApiResponse::success([
            'items' => CartItemResource::collection($summary['items']),
            'item_count' => $summary['itemCount'],
            'subtotal' => $summary['subtotal'],
        ], 'Keranjang berhasil diambil.');
    }

    public function store(
        AddToCartRequest $request,
        Product $product,
        CartService $cartService,
    ): JsonResponse {
        $cartService->add(
            $request->user(),
            $product,
            $request->validated('quantity') ?? 1,
        );

        $summary = $cartService->getCartSummary($request->user());

        return ApiResponse::success([
            'items' => CartItemResource::collection($summary['items']),
            'item_count' => $summary['itemCount'],
            'subtotal' => $summary['subtotal'],
        ], 'Produk berhasil ditambahkan ke keranjang.');
    }
}
