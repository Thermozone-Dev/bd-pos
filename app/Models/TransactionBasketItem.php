<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionBasketItem extends Model
{
    protected $fillable = [
        'transaction_basket_id',
        'item_id',
        'quantity',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_basket', 'transaction_id', 'transaction_basket_item_id');
    }

    public function discount(): HasMany
    {
        return $this->hasMany(Discount::class, 'transaction_basket_item_has_discount', 'discount_id', 'transaction_basket_item_id');
    }

    public function item(): HasOne
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }
}
