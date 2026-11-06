<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(ProductService $productService): JsonResponse
    {
        return ApiResponse::success(
            ProductResource::collection($productService->getAllForApi()),
            'Daftar produk berhasil diambil.',
        );
    }

    public function show(int $id, ProductService $productService): JsonResponse
    {
        return ApiResponse::success(
            ProductResource::make($productService->find($id)),
            'Detail produk berhasil diambil.',
        );
    }
}
