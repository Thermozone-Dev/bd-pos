<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use LogsActivity;

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
        'transaction_method_id',
        'transaction_fee',
        'cash_tendered',
        'change',
        'gross_sales',
        'vatable_sales',
        'vat',
        'vat_exempt_sales',
        'vat_exempt',
        'zero_rated_sales',
        'total_sales',
        'is_valid',
        'is_pwd',
        'is_sc',
        'is_nac',
        'is_soloparent',
    ];

    public function getActivitylogOptions(): LogOptions
    {
    return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function basket(): HasOne
    {
        return $this->hasOne(TransactionBasket::class, 'id', 'transaction_basket_id');
    }

    public function paymentMethod(): HasOne
    {
        return $this->hasOne(PaymentMethod::class, 'id', 'transaction_method_id');
    }

    public function void(): HasOne
    {
        return $this->hasOne(VoidTransaction::class, 'transaction_id', 'id');
    }

    public function return(): HasOne
    {
        return $this->hasOne(ReturnTransaction::class, 'transaction_id', 'id');
    }
}
