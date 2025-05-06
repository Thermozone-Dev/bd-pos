<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $casts = [
        'is_percentage' => 'boolean',
        'is_government_discount' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'value',
        'is_percentage',
        'is_government_discount',
    ];
}
