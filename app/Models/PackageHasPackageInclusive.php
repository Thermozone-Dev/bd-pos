<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageHasPackageInclusive extends Model
{
    protected $table = 'package_has_package_inclusives';

    protected $fillable = [
        'package_id',
        'package_inclusive_id',
        'price',
    ];

    public function packageInclusive()
    {
        return $this->belongsTo(PackageInclusive::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

}
