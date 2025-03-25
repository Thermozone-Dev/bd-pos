<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftHasTransaction extends Model
{
    protected $fillable = [
        'shift_id',
        'transaction_id',
    ];
}
