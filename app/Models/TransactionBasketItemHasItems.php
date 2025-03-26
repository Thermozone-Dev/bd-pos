<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionBasketItemHasItems extends Model
{
    protected $fillable = [
        'transaction_basket_item_id',
        'items_id',
    ];
}
