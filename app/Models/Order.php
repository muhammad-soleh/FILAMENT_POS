<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public function costumer(): BelongsTo
    {
        return $this->belongsTo(Costumer::class);
    }

    public function orderdetail(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected $fillable = [
        'costumer_id',
        'date',
        'total_price',
        'discount',
        'discount_amount',
        'total_payment',
        'status',
    ];
}
