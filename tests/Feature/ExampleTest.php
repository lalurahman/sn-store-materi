<?php

use App\Models\User;

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk();
});

it('shows login links to guests in the public navigation', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Login')
        ->assertSee('href="'.route('login').'"', escape: false);

    expect(substr_count($response->getContent(), 'href="'.route('login').'"'))->toBe(2);
});

it('shows dashboard links to authenticated users in the public navigation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('href="'.route('dashboard').'"', escape: false)
        ->assertDontSee('href="'.route('login').'"', escape: false);

    expect(substr_count($response->getContent(), 'href="'.route('dashboard').'"'))->toBe(2);
});

it('renders the storefront with ten latest products', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('data-theme-toggle')
        ->assertSee('Produk terbaru')
        ->assertSee('Headphone Wireless Studio');

    expect(substr_count($response->getContent(), 'data-product-card'))->toBe(10);
});
