<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionBasket extends Model
{
    protected $fillable = [
        'transaction_id',
        'transaction_basket_item_id',
    ];

    public function discount(): HasMany
    {
        return $this->hasMany(Discount::class);
    }
}
