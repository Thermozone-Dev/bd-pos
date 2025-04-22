<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class PaymentMethod extends Model
{
    use InteractsWithMedia;

    protected $casts = [
        'is_digital' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'is_digital',
        'is_enabled',
    ];
}
