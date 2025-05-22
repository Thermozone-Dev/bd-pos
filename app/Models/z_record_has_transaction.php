<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class z_record_has_transaction extends Model
{
    protected $fillable =[
        'z_record_id',
        'transaction-id',
    ];
}
