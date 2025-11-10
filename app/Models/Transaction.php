<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Artisan;
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
        'is_zero_rated' => 'boolean',
        'created_by' => 'integer',
    ];

    protected $fillable = [
        'processed_by',
        'terminal_id',
        'transaction_basket_id',
        'barcode',
        'transaction_method_id',
        'total_transaction_fee',
        'reference_number',
        'total_cash_tendered',
        'change',
        'gross_sales',
        'vatable_sales',
        'vat',
        'vat_exempt_sales',
        'zero_rated_sales',
        'total_sales',
        'is_valid',
        'is_pwd',
        'is_sc',
        'is_nac',
        'is_soloparent',
        'is_zero_rated',
        'or_number',
        'vat_deduction',
        'vat_adjustment',
    ];

    public function getActivitylogOptions(): LogOptions
    {
    return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
    }


    protected static function booted()
    {
        static::creating(function (Transaction $model) {
            $last_transaction = $model->orderBy('id','desc')->first();
            $batch = $last_transaction?->reset_si_batch ?? 0;
            if(!$last_transaction || $last_transaction->si_no == null){
                $si_num = 1;
            }
            else{
                $si_num = $last_transaction->si_no + 1;
                if($last_transaction->last_reseted){
                    $batch = $batch + 1;
                    $si_num = 1;
                }
                if($si_num > 999999999999){
                    Artisan::call('app:reset-sales-invoice');
                    $batch = $batch + 1;
                    $si_num = 1;
                }
            }
            $model->si_no = $si_num;
            $model->reset_si_batch = $batch;
        });
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function basket(): HasOne
    {
        return $this->hasOne(TransactionBasket::class, 'id', 'transaction_basket_id');
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(TransactionHasPaymentMethod::class, 'transaction_id', 'id');
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
