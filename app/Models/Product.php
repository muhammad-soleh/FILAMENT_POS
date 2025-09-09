<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public function orderdetail(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected $fillable = [
        'name',
        'price',
        'stock'
    ];
}
