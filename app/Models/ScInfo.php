<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScInfo extends Model
{
    protected $fillable = [
        'transaction_id',
        'name',
        'sc_id',
        'sc_tin',
    ];
}
