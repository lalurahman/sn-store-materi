<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravolt\Indonesia\Models\Village;

class CartService
{
    /**
     * @return array{items: Collection<int, CartItem>, itemCount: int, subtotal: string}
     */
    public function getCartSummary(User $user): array
    {
        $items = CartItem::query()
            ->whereBelongsTo($user)
            ->with(['product.category'])
            ->orderBy('id')
            ->get();

        $subtotalInCents = $items->sum(
            fn (CartItem $item): int => (int) str_replace('.', '', $item->product->price) * $item->quantity,
        );

        return [
            'items' => $items,
            'itemCount' => $items->sum('quantity'),
            'subtotal' => $this->formatCents($subtotalInCents),
        ];
    }

    public function add(User $user, Product $product, int $quantity): CartItem
    {
        return DB::transaction(function () use ($user, $product, $quantity): CartItem {
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->id);

            $cartItem = CartItem::query()
                ->whereBelongsTo($user)
                ->whereBelongsTo($lockedProduct)
                ->lockForUpdate()
                ->first();

            $updatedQuantity = ($cartItem?->quantity ?? 0) + $quantity;

            if ($lockedProduct->stock < 1 || $updatedQuantity > $lockedProduct->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah yang diminta melebihi stok produk yang tersedia.',
                ]);
            }

            if ($cartItem instanceof CartItem) {
                $cartItem->update(['quantity' => $updatedQuantity]);

                return $cartItem;
            }

            return CartItem::query()->create([
                'user_id' => $user->id,
                'product_id' => $lockedProduct->id,
                'quantity' => $quantity,
            ]);
        });
    }

    public function updateQuantity(User $user, CartItem $cartItem, int $quantity): CartItem
    {
        return DB::transaction(function () use ($user, $cartItem, $quantity): CartItem {
            $lockedItem = CartItem::query()
                ->whereBelongsTo($user)
                ->lockForUpdate()
                ->findOrFail($cartItem->id);
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($lockedItem->product_id);

            if ($product->stock < 1 || $quantity > $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah yang diminta melebihi stok produk yang tersedia.',
                ]);
            }

            $lockedItem->update(['quantity' => $quantity]);

            return $lockedItem;
        });
    }

    public function remove(User $user, CartItem $cartItem): void
    {
        CartItem::query()
            ->whereBelongsTo($user)
            ->whereKey($cartItem->id)
            ->firstOrFail()
            ->delete();
    }

    /**
     * @param  array{name: string, phone: string, email: string, address: string, province_code: string, city_code: string, district_code: string, village_code: string, postal_code: string, shipping_option: string}  $customer
     */
    public function checkout(
        User $user,
        array $customer,
        MidtransPaymentService $midtransPaymentService,
        RajaOngkirService $rajaOngkirService,
    ): Order {
        $village = Village::query()
            ->with('district.city.province')
            ->where('code', $customer['village_code'])
            ->firstOrFail();
        $shippingOption = $rajaOngkirService->findShippingOption(
            $village,
            $rajaOngkirService->weightForQuantity($this->getCartSummary($user)['itemCount']),
            $customer['shipping_option'],
        );

        $order = DB::transaction(function () use ($user, $customer, $midtransPaymentService, $village, $shippingOption): Order {
            $cartItems = CartItem::query()
                ->whereBelongsTo($user)
                ->with('product')
                ->orderBy('product_id')
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang kosong. Tambahkan produk sebelum checkout.',
                ]);
            }

            $products = Product::query()
                ->whereIn('id', $cartItems->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotalInCents = 0;

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);

                if (! $product instanceof Product || $product->stock < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'Stok '.($product?->name ?? $cartItem->product?->name ?? 'Produk').' tidak mencukupi. Perbarui jumlah di keranjang.',
                    ]);
                }

                $subtotalInCents += (int) str_replace('.', '', $product->price) * $cartItem->quantity;
            }

            $order = Order::query()->create([
                'order_number' => 'SN-'.Str::upper((string) Str::uuid()),
                'user_id' => $user->id,
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'customer_email' => $customer['email'],
                'shipping_address' => $customer['address'],
                'shipping_province_code' => $customer['province_code'],
                'shipping_province_name' => $village->district->city->province->name,
                'shipping_city_code' => $customer['city_code'],
                'shipping_city_name' => $village->district->city->name,
                'shipping_district_code' => $customer['district_code'],
                'shipping_district_name' => $village->district->name,
                'shipping_village_code' => $customer['village_code'],
                'shipping_village_name' => $village->name,
                'shipping_postal_code' => $customer['postal_code'],
                'shipping_cost' => $this->formatCents($shippingOption['cost'] * 100),
                'shipping_courier' => $shippingOption['courier'],
                'shipping_service' => $shippingOption['service'],
                'shipping_etd' => $shippingOption['etd'] !== '' ? $shippingOption['etd'] : null,
                'total' => $this->formatCents($subtotalInCents + $shippingOption['cost'] * 100),
                'status' => 'pending_payment',
                'payment_status' => 'pending',
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                $lineTotalInCents = (int) str_replace('.', '', $product->price) * $cartItem->quantity;

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $product->price,
                    'line_total' => $this->formatCents($lineTotalInCents),
                ]);
            }

            $midtransPaymentService->createSnapTransaction($order->load('items'));

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);

                $product->update([
                    'stock' => $product->stock - $cartItem->quantity,
                ]);
            }

            $cartItems->each->delete();

            return $order;
        });

        return $order->load('items');
    }

    private function formatCents(int $amountInCents): string
    {
        return intdiv($amountInCents, 100).'.'.str_pad((string) ($amountInCents % 100), 2, '0', STR_PAD_LEFT);
    }
}
