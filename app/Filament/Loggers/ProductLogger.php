<?php

namespace App\Filament\Loggers;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;
use Spatie\Activitylog\Models\Activity;

class ProductLogger extends Logger
{
    public static ?string $model = Product::class;

    public static function getLabel(): string | Htmlable | null
    {
        return ProductResource::getModelLabel();
    }

    public function getSubjectRoute(Activity $activity): ?string
    {
        return ProductResource::getUrl('edit', ['record' => $activity->subject_id]);
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('name'),
                Field::make('price'),
                Field::make('productType'),
                Field::make('sku'),
                Field::make('image')
                    ->media(gallery:true),
            ])
            ->relationManagers([
                //
            ]);
    }
}
