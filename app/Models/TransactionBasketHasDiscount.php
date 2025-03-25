<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionBasketHasDiscount extends Model
{
    protected $fillable = [
        'transaction_basket_id',
        'discount_id',
    ];
}
