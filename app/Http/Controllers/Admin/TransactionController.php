<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        $transactions = Order::query()
            ->withCount('items')
            ->latest()
            ->paginate(20)
            ->through(fn (Order $order): array => [
                'number' => $order->order_number,
                'url' => route('admin.transactions.show', $order->order_number),
                'customer' => $order->customer_name,
                'date' => $order->created_at->toDateString(),
                'items' => $order->items_count,
                'total' => $order->total,
                'status' => $this->statusLabel($order->status),
            ]);

        return view('pages.admin.transactions.index', [
            'transactions' => $transactions,
        ]);
    }

    public function show(string $orderNumber): View
    {
        $order = Order::query()
            ->with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('pages.admin.transactions.show', [
            'order' => $order,
            'statusLabel' => $this->statusLabel($order->status),
            'paymentStatusLabel' => match ($order->payment_status) {
                'paid' => 'Lunas',
                'failed' => 'Gagal',
                'refunded' => 'Dikembalikan',
                'chargeback' => 'Chargeback',
                default => 'Menunggu pembayaran',
            },
        ]);
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Menunggu pembayaran',
        };
    }
}
