<?php

namespace App\Filament\Loggers;

use App\Filament\Resources\DiscountResource;
use App\Models\Discount;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class DiscountLogger extends Logger
{
    public static ?string $model = Discount::class;

    public static function getLabel(): string | Htmlable | null
    {
        return DiscountResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('name'),
                Field::make('value'),
                Field::make('is_percentage'),
                Field::make('is_government_discount'),
            ])
            ->relationManagers([
                //
            ]);
    }
}
