<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageHasProduct extends Model
{
    protected $fillable = [
        'package_id',
        'product_id',
    ];
}
