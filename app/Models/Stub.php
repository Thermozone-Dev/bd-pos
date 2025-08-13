<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stub extends Model
{

    protected $table = 'stubs';

    protected $fillable = [
        'transaction_id',
        'package_inclusive_id',
        'quantity',
        'status',
        'claimed_at',
        'claimed_transact_by',
        'created_by',
        'stub_no',
    ];

    public function packageInclusive(): BelongsTo
    {
        return $this->belongsTo(PackageInclusive::class, 'package_inclusive_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }


    public function transactBy()
    {
        return $this->belongsTo(User::class, 'claimed_transact_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusName()
    {
        return $this->status == 0 ? 'Pending' : ($this->status == 1 ? 'Claimed' : 'Cancelled');
    }

    //
}
