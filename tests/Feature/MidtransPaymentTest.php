<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    config()->set('services.midtrans.server_key', 'test-server-key');
    config()->set('services.midtrans.merchant_id', 'G914114729');
});

function createPendingMidtransOrder(): array
{
    $user = User::factory()->create();
    $category = Category::query()->create([
        'name' => 'Audio',
        'slug' => 'midtrans-audio',
        'description' => null,
    ]);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Midtrans Speaker',
        'sku' => 'SN-MIDTRANS-001',
        'price' => 100000,
        'stock' => 3,
        'image_path' => 'products/midtrans-speaker.png',
    ]);
    $order = Order::query()->create([
        'order_number' => 'SN-MIDTRANS-'.Str::upper(Str::random(8)),
        'user_id' => $user->id,
        'customer_name' => 'Midtrans Customer',
        'customer_phone' => '08123456789',
        'customer_email' => 'midtrans@example.test',
        'shipping_address' => 'Jl. Midtrans No. 1',
        'total' => 200000,
        'status' => 'pending_payment',
        'payment_status' => 'pending',
    ]);
    OrderItem::query()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 2,
        'unit_price' => 100000,
        'line_total' => 200000,
    ]);

    return [$order, $product];
}

function signedMidtransNotification(Order $order, string $status, string $transactionTime): array
{
    $notification = [
        'order_id' => $order->order_number,
        'status_code' => '200',
        'gross_amount' => '200000.00',
        'transaction_id' => 'midtrans-transaction-123',
        'transaction_status' => $status,
        'transaction_time' => $transactionTime,
        'fraud_status' => 'accept',
        'currency' => 'IDR',
        'merchant_id' => 'G914114729',
    ];
    $notification['signature_key'] = hash(
        'sha512',
        $notification['order_id'].$notification['status_code'].$notification['gross_amount'].config('services.midtrans.server_key'),
    );

    return $notification;
}

it('waits for fraud review before marking a captured card payment as paid', function () {
    [$order, $product] = createPendingMidtransOrder();
    $notification = signedMidtransNotification($order, 'capture', '2026-10-30 10:00:00');
    $notification['fraud_status'] = 'challenge';

    $this->postJson(route('midtrans.notifications'), $notification)->assertOk();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'pending_payment',
        'payment_status' => 'pending',
        'gateway_status' => 'capture',
    ]);

    $notification['fraud_status'] = 'accept';
    $this->postJson(route('midtrans.notifications'), $notification)->assertOk();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'paid',
        'payment_status' => 'paid',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
    ]);
});

it('marks a paid order from a valid Midtrans settlement notification', function () {
    [$order, $product] = createPendingMidtransOrder();
    $notification = signedMidtransNotification($order, 'settlement', '2026-10-30 10:00:00');

    $this->postJson(route('midtrans.notifications'), $notification)
        ->assertOk()
        ->assertJson(['status' => 'ok']);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'paid',
        'payment_status' => 'paid',
        'gateway_status' => 'settlement',
        'gateway_transaction_id' => 'midtrans-transaction-123',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
    ]);
});

it('restores reserved stock only once when a payment expires', function () {
    [$order, $product] = createPendingMidtransOrder();
    $notification = signedMidtransNotification($order, 'expire', '2026-10-30 10:00:00');

    $this->postJson(route('midtrans.notifications'), $notification)->assertOk();
    $this->postJson(route('midtrans.notifications'), $notification)->assertOk();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'cancelled',
        'payment_status' => 'failed',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 5,
    ]);
});

it('rejects invalid signatures without changing the order or stock', function () {
    [$order, $product] = createPendingMidtransOrder();
    $notification = signedMidtransNotification($order, 'settlement', '2026-10-30 10:00:00');
    $notification['signature_key'] = str_repeat('0', 128);

    $this->postJson(route('midtrans.notifications'), $notification)->assertForbidden();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'pending_payment',
        'payment_status' => 'pending',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
    ]);
});

it('rejects notifications with an amount that does not match the order', function () {
    [$order, $product] = createPendingMidtransOrder();
    $notification = signedMidtransNotification($order, 'settlement', '2026-10-30 10:00:00');
    $notification['gross_amount'] = '100000.00';
    $notification['signature_key'] = hash(
        'sha512',
        $notification['order_id'].$notification['status_code'].$notification['gross_amount'].config('services.midtrans.server_key'),
    );

    $this->postJson(route('midtrans.notifications'), $notification)->assertUnprocessable();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'pending_payment',
        'payment_status' => 'pending',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
    ]);
});

it('does not apply an older notification after a newer transaction status', function () {
    [$order, $product] = createPendingMidtransOrder();
    $settlement = signedMidtransNotification($order, 'settlement', '2026-10-30 10:01:00');
    $pending = signedMidtransNotification($order, 'pending', '2026-10-30 10:00:00');

    $this->postJson(route('midtrans.notifications'), $settlement)->assertOk();
    $this->postJson(route('midtrans.notifications'), $pending)->assertOk();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'paid',
        'payment_status' => 'paid',
        'gateway_status' => 'settlement',
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 3,
    ]);
});
