<?php

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

/**
 * @return array{province_code: string, city_code: string, district_code: string, village_code: string, postal_code: string}
 */
function createCheckoutRegion(): array
{
    Province::query()->create(['code' => '31', 'name' => 'DKI JAKARTA']);
    City::query()->create(['code' => '3171', 'province_code' => '31', 'name' => 'KOTA JAKARTA SELATAN']);
    District::query()->create(['code' => '3171010', 'city_code' => '3171', 'name' => 'KEBAYORAN BARU']);
    Village::query()->create(['code' => '3171010001', 'district_code' => '3171010', 'name' => 'SELONG']);

    return [
        'province_code' => '31',
        'city_code' => '3171',
        'district_code' => '3171010',
        'village_code' => '3171010001',
        'postal_code' => '12110',
    ];
}

/**
 * @return array<string, mixed>
 */
function fakeRajaOngkir(int $cost = 20000): array
{
    config()->set('services.rajaongkir.key', 'test-rajaongkir-key');

    return [
        'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination*' => Http::response([
            'data' => [[
                'id' => 555,
                'city_name' => 'JAKARTA SELATAN',
                'district_name' => 'KEBAYORAN BARU',
                'subdistrict_name' => 'SELONG',
            ]],
        ]),
        'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost' => Http::response([
            'data' => [
                ['name' => 'JNE', 'code' => 'jne', 'service' => 'REG', 'description' => 'Reguler', 'cost' => $cost, 'etd' => '2-3 day'],
                ['name' => 'JNE', 'code' => 'jne', 'service' => 'YES', 'description' => 'Kilat', 'cost' => $cost * 2, 'etd' => '1 day'],
            ],
        ]),
    ];
}

it('redirects guests to login before they can use the cart or checkout', function () {
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Wireless Speaker',
        'sku' => 'SN-AUD-GUEST',
        'price' => 899000,
        'stock' => 4,
        'image_path' => 'products/speaker.png',
    ]);

    $this->get(route('cart.index'))->assertRedirect(route('login'));
    $this->get(route('checkout.create'))->assertRedirect(route('login'));
    $this->post(route('cart.items.store', $product))->assertRedirect(route('login'));
    $this->post(route('checkout.store'))->assertRedirect(route('login'));

    $this->assertDatabaseCount('cart_items', 0);
    $this->assertDatabaseCount('orders', 0);
});

it('adds an in-stock product to the signed-in user cart and displays its details', function () {
    $user = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Wireless Speaker',
        'sku' => 'SN-AUD-CART',
        'price' => 899000,
        'stock' => 4,
        'image_path' => 'products/speaker.png',
    ]);

    $this->actingAs($user)
        ->from(route('products.index'))
        ->post(route('cart.items.store', $product), ['quantity' => 2])
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('status', 'Produk berhasil ditambahkan ke keranjang.');
    $this->actingAs($user)
        ->post(route('cart.items.store', $product), ['quantity' => 1])
        ->assertRedirect();

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 4,
    ]);

    $this->actingAs($user)
        ->get(route('cart.index'))
        ->assertOk()
        ->assertSee('Wireless Speaker')
        ->assertSee('3 item di keranjang')
        ->assertSee('Rp2.697.000');
});

it('rejects adding a product when the requested cart quantity exceeds its stock', function () {
    $user = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Low Stock Speaker',
        'sku' => 'SN-AUD-LOW',
        'price' => 500000,
        'stock' => 1,
        'image_path' => 'products/low-speaker.png',
    ]);

    $this->actingAs($user)
        ->post(route('cart.items.store', $product), ['quantity' => 2])
        ->assertSessionHasErrors('quantity');

    $this->assertDatabaseCount('cart_items', 0);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 1,
    ]);
});

it('rejects adding an out-of-stock product to the cart', function () {
    $user = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Unavailable Speaker',
        'sku' => 'SN-AUD-EMPTY',
        'price' => 500000,
        'stock' => 0,
        'image_path' => 'products/unavailable-speaker.png',
    ]);

    $this->actingAs($user)
        ->post(route('cart.items.store', $product))
        ->assertSessionHasErrors('quantity');

    $this->assertDatabaseCount('cart_items', 0);
});

it('updates cart quantities within stock and prevents one user changing another users cart', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Cart Speaker',
        'sku' => 'SN-AUD-UPDATE',
        'price' => 500000,
        'stock' => 4,
        'image_path' => 'products/cart-speaker.png',
    ]);
    $cartItem = CartItem::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($otherUser)
        ->put(route('cart.items.update', $cartItem), ['quantity' => 2])
        ->assertNotFound();

    $this->actingAs($user)
        ->put(route('cart.items.update', $cartItem), ['quantity' => 3])
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
        'user_id' => $user->id,
        'quantity' => 3,
    ]);

    $this->actingAs($user)
        ->put(route('cart.items.update', $cartItem), ['quantity' => 5])
        ->assertSessionHasErrors('quantity');

    $this->assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
        'quantity' => 3,
    ]);

    $this->actingAs($user)
        ->delete(route('cart.items.destroy', $cartItem))
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
});

it('creates an order from cart items, copies account details, and reserves stock', function () {
    $region = createCheckoutRegion();
    $user = User::factory()->create([
        'name' => 'Dina Pelanggan',
        'email' => 'dina@example.test',
        'phone' => '+628123456789',
        'address' => 'Jl. Anggrek No. 8',
    ]);
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Checkout Speaker',
        'sku' => 'SN-AUD-CHECKOUT',
        'price' => 899000,
        'stock' => 5,
        'image_path' => 'products/checkout-speaker.png',
    ]);
    $cartItem = CartItem::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('checkout.create'))
        ->assertOk()
        ->assertSee('value="Dina Pelanggan"', escape: false)
        ->assertSee('value="+628123456789"', escape: false)
        ->assertSee('value="dina@example.test"', escape: false)
        ->assertSee('Jl. Anggrek No. 8')
        ->assertSee('id="province_code"', escape: false)
        ->assertSee('name="city_code" disabled', escape: false)
        ->assertSee('>DKI JAKARTA</option>', escape: false)
        ->assertSee('Pembayaran akan diproses dengan aman melalui Midtrans');

    config()->set('services.midtrans.server_key', 'test-server-key');
    Http::preventStrayRequests();
    Http::fake([
        ...fakeRajaOngkir(),
        'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
            'token' => 'snap-test-token',
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/test-token',
        ]),
    ]);

    $response = $this->actingAs($user)->post(route('checkout.store'), [
        'name' => 'Dina Pelanggan',
        'phone' => '+628123456789',
        'email' => 'dina@example.test',
        'address' => 'Jl. Anggrek No. 8',
        ...$region,
        'shipping_option' => 'jne|REG',
    ]);
    $order = Order::query()->where('user_id', $user->id)->firstOrFail();

    $response->assertRedirect(route('orders.show', $order->order_number));
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'order_number' => $order->order_number,
        'customer_name' => 'Dina Pelanggan',
        'customer_phone' => '+628123456789',
        'customer_email' => 'dina@example.test',
        'shipping_address' => 'Jl. Anggrek No. 8',
        'shipping_province_code' => '31',
        'shipping_province_name' => 'DKI JAKARTA',
        'shipping_city_name' => 'KOTA JAKARTA SELATAN',
        'shipping_district_name' => 'KEBAYORAN BARU',
        'shipping_village_name' => 'SELONG',
        'shipping_postal_code' => '12110',
        'shipping_cost' => '20000.00',
        'shipping_courier' => 'jne',
        'shipping_service' => 'REG',
        'total' => '1818000.00',
        'status' => 'pending_payment',
        'payment_status' => 'pending',
    ]);
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => 'Checkout Speaker',
        'quantity' => 2,
        'unit_price' => '899000.00',
        'line_total' => '1798000.00',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
    ]);
    $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'snap_token' => 'snap-test-token',
        'payment_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/test-token',
    ]);
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://app.sandbox.midtrans.com/snap/v1/transactions'
        && $request['transaction_details']['gross_amount'] === 1818000
        && collect($request['item_details'])->contains(fn (array $item): bool => $item['id'] === 'SHIPPING' && $item['price'] === 20000)
        && $request['customer_details']['email'] === 'dina@example.test'
        && $request['customer_details']['shipping_address']['address'] === 'Jl. Anggrek No. 8, SELONG, KEBAYORAN BARU, KOTA JAKARTA SELATAN, DKI JAKARTA, 12110');

    $this->actingAs($user)
        ->get(route('orders.show', $order->order_number))
        ->assertOk()
        ->assertSee($order->order_number)
        ->assertSee('Menunggu pembayaran')
        ->assertSee('Bayar dengan Midtrans')
        ->assertSee('Checkout Speaker')
        ->assertSee('SELONG, KEBAYORAN BARU, KOTA JAKARTA SELATAN, DKI JAKARTA 12110');
});

it('loads each region level from the selected parent', function () {
    $region = createCheckoutRegion();

    $this->actingAs(User::factory()->create())
        ->getJson(route('checkout.regions.cities', $region['province_code']))
        ->assertOk()
        ->assertExactJson([['code' => '3171', 'name' => 'KOTA JAKARTA SELATAN']]);

    $this->getJson(route('checkout.regions.districts', $region['city_code']))
        ->assertOk()
        ->assertExactJson([['code' => '3171010', 'name' => 'KEBAYORAN BARU']]);

    $this->getJson(route('checkout.regions.villages', $region['district_code']))
        ->assertOk()
        ->assertExactJson([['code' => '3171010001', 'name' => 'SELONG']]);

    $this->getJson(route('checkout.regions.cities', '99'))->assertNotFound();
});

it('rejects a city that does not belong to the selected province', function () {
    $region = createCheckoutRegion();
    Province::query()->create(['code' => '32', 'name' => 'JAWA BARAT']);
    $user = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Region Validation Speaker',
        'sku' => 'SN-AUD-REGION',
        'price' => 500000,
        'stock' => 2,
        'image_path' => 'products/region-speaker.png',
    ]);
    CartItem::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->post(route('checkout.store'), [
            'name' => 'Test Customer',
            'phone' => '08123456789',
            'email' => 'customer@example.test',
            'address' => 'Jl. Test No. 1',
            ...$region,
            'province_code' => '32',
        ])
        ->assertSessionHasErrors('city_code');

    $this->assertDatabaseCount('orders', 0);
});

it('keeps the cart and stock when Midtrans rejects payment creation', function () {
    $region = createCheckoutRegion();
    $user = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Rejected Checkout Speaker',
        'sku' => 'SN-AUD-REJECTED',
        'price' => 500000,
        'stock' => 3,
        'image_path' => 'products/rejected-speaker.png',
    ]);
    $cartItem = CartItem::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    config()->set('services.midtrans.server_key', 'test-server-key');
    Http::preventStrayRequests();
    Http::fake([
        ...fakeRajaOngkir(),
        'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
            'message' => 'Unauthorized',
        ], 401),
    ]);

    $this->actingAs($user)
        ->from(route('checkout.create'))
        ->post(route('checkout.store'), [
            'name' => 'Test Customer',
            'phone' => '08123456789',
            'email' => 'customer@example.test',
            'address' => 'Jl. Test No. 1',
            ...$region,
            'shipping_option' => 'jne|REG',
        ])
        ->assertRedirect(route('checkout.create'))
        ->assertSessionHasErrors('payment');

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
        'quantity' => 2,
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
    ]);
});

it('keeps the cart and stock unchanged when checkout finds insufficient stock', function () {
    $region = createCheckoutRegion();
    $user = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Stock Changed Speaker',
        'sku' => 'SN-AUD-CHANGED',
        'price' => 500000,
        'stock' => 1,
        'image_path' => 'products/changed-speaker.png',
    ]);
    $cartItem = CartItem::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    Http::preventStrayRequests();
    Http::fake(fakeRajaOngkir());

    $this->actingAs($user)
        ->post(route('checkout.store'), [
            'name' => 'Test Customer',
            'phone' => '08123456789',
            'email' => 'customer@example.test',
            'address' => 'Jl. Test No. 1',
            ...$region,
            'shipping_option' => 'jne|REG',
        ])
        ->assertSessionHasErrors('cart');

    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);
    $this->assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
        'quantity' => 2,
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 1,
    ]);
});

it('validates required checkout contact details', function () {
    $user = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Required Fields Speaker',
        'sku' => 'SN-AUD-REQUIRED',
        'price' => 500000,
        'stock' => 2,
        'image_path' => 'products/required-speaker.png',
    ]);
    CartItem::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->post(route('checkout.store'), [])
        ->assertSessionHasErrors([
            'name',
            'phone',
            'email',
            'address',
            'province_code',
            'city_code',
            'district_code',
            'village_code',
            'postal_code',
            'shipping_option',
        ]);

    $this->assertDatabaseCount('orders', 0);
});

it('returns shipping options from RajaOngkir for a selected village', function () {
    $region = createCheckoutRegion();
    $user = User::factory()->create();
    $category = Category::query()->create(['name' => 'Audio', 'slug' => 'audio', 'description' => null]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Shipping Speaker',
        'sku' => 'SN-AUD-SHIP',
        'price' => 500000,
        'stock' => 5,
        'image_path' => 'products/ship.png',
    ]);
    CartItem::query()->create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 2]);

    Http::preventStrayRequests();
    Http::fake(fakeRajaOngkir(15000));

    $this->actingAs($user)
        ->postJson(route('checkout.shipping-costs'), ['village_code' => $region['village_code']])
        ->assertOk()
        ->assertJsonPath('options.0.key', 'jne|REG')
        ->assertJsonPath('options.0.cost', 15000);

    Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/calculate/domestic-cost')
        && $request['origin'] === '79234'
        && $request['destination'] === 555
        && $request['weight'] === 1000
        && $request->hasHeader('key', 'test-rajaongkir-key'));
});

it('rejects checkout when the selected shipping service is unavailable', function () {
    $region = createCheckoutRegion();
    $user = User::factory()->create();
    $category = Category::query()->create(['name' => 'Audio', 'slug' => 'audio', 'description' => null]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Unavailable Shipping Speaker',
        'sku' => 'SN-AUD-NOSHIP',
        'price' => 500000,
        'stock' => 5,
        'image_path' => 'products/noship.png',
    ]);
    CartItem::query()->create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

    Http::preventStrayRequests();
    Http::fake(fakeRajaOngkir());

    $this->actingAs($user)
        ->post(route('checkout.store'), [
            'name' => 'Test Customer',
            'phone' => '08123456789',
            'email' => 'customer@example.test',
            'address' => 'Jl. Test No. 1',
            ...$region,
            'shipping_option' => 'jnt|EZ',
        ])
        ->assertSessionHasErrors('shipping_option');

    $this->assertDatabaseCount('orders', 0);
});

it('does not show an order to a different signed-in user', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $order = Order::query()->create([
        'order_number' => 'SN-PRIVATE-ORDER',
        'user_id' => $owner->id,
        'customer_name' => 'Private Customer',
        'customer_phone' => '08123456789',
        'customer_email' => 'private@example.test',
        'shipping_address' => 'Private address',
        'total' => 500000,
        'status' => 'pending_payment',
        'payment_status' => 'pending',
    ]);

    $this->actingAs($otherUser)
        ->get(route('orders.show', $order->order_number))
        ->assertNotFound();
});

it('escapes customer-provided content on the order confirmation page', function () {
    $user = User::factory()->create();
    $order = Order::query()->create([
        'order_number' => 'SN-ESCAPED-ORDER',
        'user_id' => $user->id,
        'customer_name' => '<script>alert(1)</script>',
        'customer_phone' => '08123456789',
        'customer_email' => 'customer@example.test',
        'shipping_address' => '<img src=x onerror=alert(1)>',
        'total' => 500000,
        'status' => 'pending_payment',
        'payment_status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(route('orders.show', $order->order_number))
        ->assertOk()
        ->assertSee('<script>alert(1)</script>')
        ->assertSee('<img src=x onerror=alert(1)>')
        ->assertDontSee('<script>alert(1)</script>', escape: false)
        ->assertDontSee('<img src=x onerror=alert(1)>', escape: false);
});
