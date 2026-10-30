<?php

namespace App\Http\Controllers;

use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransPaymentService $midtransPaymentService): JsonResponse
    {
        $notification = $request->validate([
            'order_id' => ['required', 'string', 'max:50'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'numeric'],
            'signature_key' => ['required', 'string', 'size:128'],
            'transaction_id' => ['nullable', 'string'],
            'transaction_status' => ['required', 'string', 'max:30'],
            'transaction_time' => ['required', 'date'],
            'fraud_status' => ['nullable', 'string'],
            'currency' => ['required', 'in:IDR'],
            'merchant_id' => ['required', 'string', 'max:30'],
        ]);

        if (! $midtransPaymentService->hasValidSignature($notification)) {
            return response()->json(['message' => 'Signature not valid.'], 403);
        }

        if (! $midtransPaymentService->handleNotification($notification)) {
            return response()->json(['message' => 'Notification does not match an order.'], 422);
        }

        return response()->json(['status' => 'ok']);
    }
}
