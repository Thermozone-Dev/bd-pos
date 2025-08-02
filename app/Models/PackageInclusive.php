<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageInclusive extends Model
{
    protected $table = 'package_inclusives';

    protected $fillable = [
        'stall_id',
        'name',
        'price',
        'description',
        'is_active',
    ];

    public function packageHasPackageInclusives() : HasMany
    {
        return $this->hasMany(PackageInclusiveHasProducts::class, 'package_inclusives_id');
    }

}
