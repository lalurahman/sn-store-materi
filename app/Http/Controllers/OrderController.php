<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(Request $request, string $orderNumber): View
    {
        $order = Order::query()
            ->whereBelongsTo($request->user())
            ->where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        return view('pages.public.orders.show', [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'order' => $order,
            'searchQuery' => '',
        ]);
    }
}
