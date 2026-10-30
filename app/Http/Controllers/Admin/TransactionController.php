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
                'customer' => $order->customer_name,
                'date' => $order->created_at->toDateString(),
                'items' => $order->items_count,
                'total' => $order->total,
                'status' => match ($order->status) {
                    'paid' => 'Dibayar',
                    'processing' => 'Diproses',
                    'shipped' => 'Dikirim',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                    default => 'Menunggu pembayaran',
                },
            ]);

        return view('pages.admin.transactions.index', [
            'transactions' => $transactions,
        ]);
    }
}
