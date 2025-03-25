<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionBasketItemHasDiscount extends Model
{
    protected $fillable = [
        'transaction_basket_item_id',
        'discount_id',
    ];
}
