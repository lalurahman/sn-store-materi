<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk();
});

it('renders the storefront with ten latest products', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Produk terbaru')
        ->assertSee('Headphone Wireless Studio');

    expect(substr_count($response->getContent(), 'data-product-card'))->toBe(10);
});
