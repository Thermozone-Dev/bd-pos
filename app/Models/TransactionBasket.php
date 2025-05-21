<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionBasket extends Model
{
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'id', 'transaction_basket_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionBasketItem::class, 'transaction_basket_id', 'id');
    }

    public function discount(): HasMany
    {
        return $this->hasMany(Discount::class, 'transaction_basket_has_discount', 'discount_id', 'transaction_basket_id');
    }
}
