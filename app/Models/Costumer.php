<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Costumer extends Model
{

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];
}
