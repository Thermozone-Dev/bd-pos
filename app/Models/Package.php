<?php

namespace App\Models;

use App\Enums\ProductTaxCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Package extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;

    protected $casts = [
        'product_tax_category' => ProductTaxCategory::class,
    ];

    protected $fillable = [
        'name',
        'price',
        'product_tax_category',
        'pax',
        'base_rate_name',
        'base_price',
    ];

    public function getActivitylogOptions(): LogOptions
    {
    return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'package_has_products', 'package_id', 'product_id')->withPivot('id');
    }

    public function productsJunction(): HasMany
    {
        return $this->hasMany(PackageHasProduct::class, 'package_id', 'id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id', 'package_id');
    }

    public function packageHasPackageInclusive(): HasMany
    {
        return $this->hasMany(PackageHasPackageInclusive::class, 'package_id', 'id');
    }

}
