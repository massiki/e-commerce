<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'invoice_number',
    'recipient_name',
    'phone',
    'province',
    'city',
    'district',
    'postal_code',
    'full_address',
    'coupon_code',
    'coupon_discount',
    'subtotal',
    'shipping_cost',
    'total',
    'payment_method',
    'snap_token',
    'payment_status',
    'status',
])]
class Order extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
