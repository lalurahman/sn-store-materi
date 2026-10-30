<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Category;
use App\Services\CartService;
use App\Services\MidtransPaymentException;
use App\Services\MidtransPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request, CartService $cartService): View|RedirectResponse
    {
        $cartSummary = $cartService->getCartSummary($request->user());

        if ($cartSummary['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Keranjang kosong. Tambahkan produk sebelum checkout.');
        }

        return view('pages.public.checkout.index', [
            ...$cartSummary,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'searchQuery' => '',
            'user' => $request->user(),
        ]);
    }

    public function store(
        CheckoutRequest $request,
        CartService $cartService,
        MidtransPaymentService $midtransPaymentService,
    ): RedirectResponse {
        try {
            $order = $cartService->checkout($request->user(), $request->validated(), $midtransPaymentService);
        } catch (MidtransPaymentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['payment' => $exception->getMessage()]);
        }

        return redirect()->route('orders.show', $order->order_number);
    }
}
