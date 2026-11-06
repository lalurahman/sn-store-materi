<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function success(
        JsonResource|array|null $data,
        string $message,
        int $code = Response::HTTP_OK,
    ): JsonResponse {
        return response()->json([
            'code' => $code,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error(
        string $message,
        int $code,
        array $data = [],
    ): JsonResponse {
        return response()->json([
            'code' => $code,
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
