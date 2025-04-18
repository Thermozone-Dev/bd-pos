<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionBasket extends Model
{
    protected $fillable = [
        'transaction_id',
        'transaction_basket_item_id',
    ];

    public function transation(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function discount(): HasMany
    {
        return $this->hasMany(Discount::class, 'transaction_basket_has_discount', 'discount_id', 'transaction_basket_id');
    }
}
