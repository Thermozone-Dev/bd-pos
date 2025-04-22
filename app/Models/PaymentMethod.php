<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PaymentMethod extends Model implements HasMedia
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
