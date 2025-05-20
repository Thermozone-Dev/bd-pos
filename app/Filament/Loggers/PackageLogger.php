<?php

namespace App\Filament\Loggers;

use App\Filament\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class PackageLogger extends Logger
{
    public static ?string $model = Package::class;

    public static function getLabel(): string | Htmlable | null
    {
        return PackageResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('name'),
                Field::make('price'),
                Field::make('image')
                    ->media(gallery:true),
            ])
            ->relationManagers([
                //
            ]);
    }
}
