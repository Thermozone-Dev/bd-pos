<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
