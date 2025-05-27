<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity, HasFactory;

    protected $casts = [
        'is_digital' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'is_digital',
        'is_enabled',
    ];

    public function getActivitylogOptions(): LogOptions
    {
    return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
    }
}
