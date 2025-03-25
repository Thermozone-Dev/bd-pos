<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionBasketItem extends Model
{
    protected $fillable = [
        'transaction_basket_id',
        'quantity',
    ];
}
