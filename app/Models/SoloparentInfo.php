<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoloparentInfo extends Model
{
    protected $fillable = [
        'transaction_id',
        'name',
        'spic_id',
        'child_name',
        'child_age',
        'child_birthday',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }
}
