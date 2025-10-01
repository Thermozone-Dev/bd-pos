<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLock extends Model
{
    protected $fillable = [
        'date',
        'is_locked',
    ];
}
