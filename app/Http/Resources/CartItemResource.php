<?php

namespace App\Http\Resources;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CartItem
 */
class CartItemResource extends JsonResource
{
    /**
     * @return array{id: int, quantity: int, unit_price: string, line_total: string, product: ProductResource}
     */
    public function toArray(Request $request): array
    {
        $unitPriceInCents = (int) str_replace('.', '', $this->product->price);

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'unit_price' => $this->product->price,
            'line_total' => $this->formatCents($unitPriceInCents * $this->quantity),
            'product' => ProductResource::make($this->product),
        ];
    }

    private function formatCents(int $amountInCents): string
    {
        return intdiv($amountInCents, 100).'.'.str_pad((string) ($amountInCents % 100), 2, '0', STR_PAD_LEFT);
    }
}
