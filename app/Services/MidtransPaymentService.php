<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MidtransPaymentService
{
    public function createSnapTransaction(Order $order): void
    {
        $serverKey = config('services.midtrans.server_key');

        if (! is_string($serverKey) || $serverKey === '') {
            throw new MidtransPaymentException('Konfigurasi Midtrans belum lengkap. Atur MIDTRANS_SERVER_KEY terlebih dahulu.');
        }

        $items = $order->items;
        $itemDetails = $items->map(function ($item): array {
            $unitPrice = $this->integerRupiahAmount((string) $item->unit_price);

            return [
                'id' => $item->sku,
                'price' => $unitPrice,
                'quantity' => $item->quantity,
                'name' => mb_substr($item->product_name, 0, 50),
            ];
        })->all();

        $total = $this->integerRupiahAmount((string) $order->total);

        if (array_sum(array_map(
            fn (array $item): int => $item['price'] * $item['quantity'],
            $itemDetails,
        )) !== $total) {
            throw new MidtransPaymentException('Total pesanan tidak valid untuk pembayaran Midtrans.');
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'shipping_address' => [
                    'first_name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'address' => $order->shipping_address,
                ],
            ],
            'item_details' => $itemDetails,
        ];

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withBasicAuth($serverKey, '')
                ->connectTimeout(3)
                ->timeout(8)
                ->post(config('services.midtrans.base_url').'/snap/v1/transactions', $payload);
        } catch (ConnectionException $exception) {
            throw new MidtransPaymentException(
                'Tidak dapat terhubung ke Midtrans. Keranjang dan stok tidak diubah; silakan coba kembali.',
                previous: $exception,
            );
        }

        if (! $response->successful()) {
            throw new MidtransPaymentException(
                'Midtrans menolak pembuatan pembayaran (HTTP '.$response->status().'). Keranjang dan stok tidak diubah.',
            );
        }

        $snapToken = $response->json('token');
        $paymentUrl = $response->json('redirect_url');

        if (! is_string($snapToken) || $snapToken === '' || ! is_string($paymentUrl) || $paymentUrl === '') {
            throw new MidtransPaymentException('Respons Midtrans tidak berisi token atau URL pembayaran yang valid.');
        }

        $order->forceFill([
            'snap_token' => $snapToken,
            'payment_url' => $paymentUrl,
        ])->save();
    }

    /**
     * @param  array{order_id: string, status_code: string, gross_amount: string, signature_key: string}  $notification
     */
    public function hasValidSignature(array $notification): bool
    {
        $serverKey = config('services.midtrans.server_key');

        if (! is_string($serverKey) || $serverKey === '') {
            return false;
        }

        $expectedSignature = hash(
            'sha512',
            $notification['order_id'].$notification['status_code'].$notification['gross_amount'].$serverKey,
        );

        return hash_equals($expectedSignature, strtolower($notification['signature_key']));
    }

    /**
     * @param  array{
     *     order_id: string,
     *     transaction_id?: string|null,
     *     transaction_status: string,
     *     transaction_time: string,
     *     fraud_status?: string|null,
     *     gross_amount: string,
     *     currency: string,
     *     merchant_id: string
     * }  $notification
     */
    public function handleNotification(array $notification): bool
    {
        if (
            $notification['currency'] !== 'IDR'
            || $notification['merchant_id'] !== config('services.midtrans.merchant_id')
        ) {
            return false;
        }

        return DB::transaction(function () use ($notification): bool {
            $order = Order::query()
                ->where('order_number', $notification['order_id'])
                ->lockForUpdate()
                ->first();

            if (! $order instanceof Order) {
                return false;
            }

            if (number_format((float) $order->total, 2, '.', '') !== number_format((float) $notification['gross_amount'], 2, '.', '')) {
                return false;
            }

            $gatewayStatus = strtolower($notification['transaction_status']);

            if (
                $order->gateway_status !== null
                && $this->statusPriority($gatewayStatus) < $this->statusPriority($order->gateway_status)
            ) {
                return true;
            }

            $updates = [
                'gateway_status' => $gatewayStatus,
                'gateway_transaction_time' => Carbon::parse($notification['transaction_time']),
                'gateway_transaction_id' => $notification['transaction_id'] ?? $order->gateway_transaction_id,
            ];

            if ($gatewayStatus === 'settlement' || ($gatewayStatus === 'capture' && ($notification['fraud_status'] ?? null) === 'accept')) {
                if ($order->status !== 'cancelled') {
                    $updates['status'] = 'paid';
                    $updates['payment_status'] = 'paid';
                }
            } elseif (in_array($gatewayStatus, ['cancel', 'deny', 'expire', 'failure'], true)) {
                if ($order->status !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->product_id !== null) {
                            $product = $item->product()->lockForUpdate()->first();

                            if ($product !== null) {
                                $product->increment('stock', $item->quantity);
                            }
                        }
                    }

                    $updates['status'] = 'cancelled';
                    $updates['payment_status'] = 'failed';
                }
            } elseif (in_array($gatewayStatus, ['refund', 'partial_refund'], true)) {
                $updates['payment_status'] = 'refunded';
            } elseif (in_array($gatewayStatus, ['chargeback', 'partial_chargeback'], true)) {
                $updates['payment_status'] = 'chargeback';
            } elseif ($order->status === 'pending_payment') {
                $updates['payment_status'] = 'pending';
            }

            $order->forceFill($updates)->save();

            return true;
        });
    }

    private function integerRupiahAmount(string $amount): int
    {
        $normalizedAmount = number_format((float) $amount, 2, '.', '');

        if (! str_ends_with($normalizedAmount, '.00')) {
            throw new MidtransPaymentException('Harga produk harus berupa nominal Rupiah tanpa pecahan.');
        }

        return (int) substr($normalizedAmount, 0, -3);
    }

    private function statusPriority(string $status): int
    {
        return match ($status) {
            'pending' => 1,
            'authorize' => 2,
            'capture' => 3,
            'settlement' => 4,
            'cancel', 'deny', 'expire', 'failure' => 5,
            'refund', 'partial_refund' => 6,
            'chargeback', 'partial_chargeback' => 7,
            default => 0,
        };
    }
}
