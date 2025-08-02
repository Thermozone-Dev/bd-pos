<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageInclusiveHasProducts extends Model
{

    protected $table = 'package_inclusive_has_products';

    protected $fillable = [
        'package_inclusives_id',
        'product_id',
        'qty',
    ];

    public function packageInclusive()
    {
        return $this->belongsTo(PackageInclusive::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class,);
    }

}
