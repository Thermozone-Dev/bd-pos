<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $casts = [
        'is_percentage' => 'boolean',
        'is_flat_value' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'value',
    ];
}
