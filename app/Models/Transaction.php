<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $casts = [
        'is_valid' => 'boolean',
        'is_pwd' => 'boolean',
        'is_sc' => 'boolean',
        'is_nac' => 'boolean',
        'is_soloparent' => 'boolean',
        'created_by' => 'integer',
    ];

    protected $fillable = [
        'processed_by',
        'transaction_basket_id',
        'barcode',
        'transaction_method',
        'transaction_fee',
        'cash_tendered',
        'change',
        'vatable_sales',
        'vat',
        'vat_exempt_sales',
        'vat_exempt',
        'zero_rated_sales',
        'is_valid',
        'is_pwd',
        'is_sc',
        'is_nac',
        'is_soloparent',
    ];

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
