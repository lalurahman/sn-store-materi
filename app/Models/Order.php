<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_number',
    'user_id',
    'customer_name',
    'customer_phone',
    'customer_email',
    'shipping_address',
    'shipping_province_code',
    'shipping_province_name',
    'shipping_city_code',
    'shipping_city_name',
    'shipping_district_code',
    'shipping_district_name',
    'shipping_village_code',
    'shipping_village_name',
    'shipping_postal_code',
    'shipping_cost',
    'shipping_courier',
    'shipping_service',
    'shipping_etd',
    'total',
    'status',
    'payment_status',
    'snap_token',
    'payment_url',
    'gateway_transaction_id',
    'gateway_status',
    'gateway_transaction_time',
])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'gateway_transaction_time' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
