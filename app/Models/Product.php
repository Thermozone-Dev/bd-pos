<?php

namespace App\Models;

use App\Enums\ProductTaxCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    protected $casts = [
        'product_tax_category' => ProductTaxCategory::class,
    ];

    protected $fillable = [
        'name',
        'price',
        'product_type_id',
        'product_tax_category',
        'pax',
        'sku',
    ];

    public function getActivitylogOptions(): LogOptions
    {
    return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_has_products', 'product_id', 'package_id');
    }

    public function productType(): HasOne
    {
        return $this->hasOne(ProductType::class, 'id', 'product_type_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id', 'product_id');
    }
}
