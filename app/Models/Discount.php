<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Discount extends Model
{
    use LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
    return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
    }
}
