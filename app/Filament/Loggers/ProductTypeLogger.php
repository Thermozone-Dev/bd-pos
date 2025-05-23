<?php

namespace App\Filament\Loggers;

use App\Filament\Resources\ProductTypeResource;
use App\Models\ProductType;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class ProductTypeLogger extends Logger
{
    public static ?string $model = ProductType::class;

    public static function getLabel(): string | Htmlable | null
    {
        return ProductTypeResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('name')
                    ->label('Name'),
            ])
            ->relationManagers([
                //
            ]);
    }
}
