<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Hash;

test('guests can register and receive a safe user response', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $response = $this->postJson(route('api.register'), [
        'name' => 'API Customer',
        'email' => 'api-customer@example.test',
        'password' => 'StrongPassword123!',
        'password_confirmation' => 'StrongPassword123!',
    ]);

    $response->assertCreated()
        ->assertExactJson([
            'code' => 201,
            'status' => 'success',
            'message' => 'Registrasi berhasil.',
            'data' => [
                'user' => [
                    'id' => User::query()->where('email', 'api-customer@example.test')->value('id'),
                    'name' => 'API Customer',
                    'email' => 'api-customer@example.test',
                ],
            ],
        ]);

    $user = User::query()->where('email', 'api-customer@example.test')->firstOrFail();

    expect(Hash::check('StrongPassword123!', $user->password))->toBeTrue()
        ->and($user->hasRole('customer'))->toBeTrue();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

test('registration rejects a duplicate email address', function () {
    User::factory()->create(['email' => 'existing@example.test']);

    $this->postJson(route('api.register'), [
        'name' => 'Existing User',
        'email' => 'existing@example.test',
        'password' => 'StrongPassword123!',
        'password_confirmation' => 'StrongPassword123!',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('email');

    $this->assertDatabaseCount('users', 1);
});

test('registration returns 422 when the password confirmation does not match', function () {
    $this->postJson(route('api.register'), [
        'name' => 'API Customer',
        'email' => 'api-customer@example.test',
        'password' => 'StrongPassword123!',
        'password_confirmation' => 'DifferentPassword123!',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('password');

    $this->assertDatabaseCount('users', 0);
});

test('users can log in and receive a working bearer token', function () {
    $user = User::factory()->create([
        'email' => 'api-user@example.test',
        'password' => 'StrongPassword123!',
    ]);

    $response = $this->postJson(route('api.login'), [
        'email' => 'API-USER@example.test',
        'password' => 'StrongPassword123!',
    ]);

    $response->assertOk()
        ->assertJsonPath('code', 200)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Login berhasil.')
        ->assertJsonPath('data.user.id', $user->id)
        ->assertJsonPath('data.user.email', 'api-user@example.test')
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonStructure(['data' => ['access_token']]);

    $accessToken = $response->json('data.access_token');

    $this->assertDatabaseCount('personal_access_tokens', 1);

    $this->withToken($accessToken)
        ->getJson(route('api.user'))
        ->assertOk()
        ->assertJsonPath('id', $user->id);
});

test('login returns 401 when the credentials are invalid', function () {
    User::factory()->create([
        'email' => 'api-user@example.test',
        'password' => 'StrongPassword123!',
    ]);

    $this->postJson(route('api.login'), [
        'email' => 'api-user@example.test',
        'password' => 'WrongPassword123!',
    ])->assertUnauthorized()
        ->assertExactJson([
            'code' => 401,
            'status' => 'error',
            'message' => 'Email atau password tidak valid.',
            'data' => [],
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});
