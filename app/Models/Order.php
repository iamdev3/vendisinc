<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'subtotal'                  => 'decimal:2',
        'tax_amount'                => 'decimal:2',
        'discount_amount'           => 'decimal:2',
        'total_amount'              => 'decimal:2',
        'total_profit'              => 'decimal:2',
        'profit_margin'             => 'decimal:2',
        'order_date'                => 'datetime',
        'expected_delivery_date'    => 'datetime',
        'delivered_at'              => 'datetime',
        'customer_information'      => 'array',
        // 'quantity_ordered'          => 'integer',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function retailor(): BelongsTo
    {
        return $this->belongsTo(Retailor::class, 'retailer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }
}
