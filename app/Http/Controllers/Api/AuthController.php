<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ]);

        $user->assignRole('customer');

        event(new Registered($user));

        return ApiResponse::success([
            'user' => $this->userData($user),
        ], 'Registrasi berhasil.', Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $user = User::query()
            ->where('email', Str::lower($credentials['email']))
            ->first();

        if (! $user instanceof User || ! Hash::check($credentials['password'], $user->password)) {
            return ApiResponse::error(
                'Email atau password tidak valid.',
                Response::HTTP_UNAUTHORIZED,
            );
        }

        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::success([
            'user' => $this->userData($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Login berhasil.');
    }

    /**
     * @return array{id: int, name: string, email: string}
     */
    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
