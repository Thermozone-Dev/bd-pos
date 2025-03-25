<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NacInfo extends Model
{
    protected $fillable = [
        'transaction_id',
        'name',
        'pnstm_id',
    ];
}
