<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHasPaymentMethod extends Model
{
    protected $fillable = [
        'transaction_id',
        'payment_method_id',
        'cash_tendered',
        'transaction_fee',
        'reference_number',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
