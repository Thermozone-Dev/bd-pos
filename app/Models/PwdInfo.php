<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PwdInfo extends Model
{
    protected $fillable = [
        'transaction_id',
        'name',
        'pwd_id',
        'pwd_tin',
    ];
}
