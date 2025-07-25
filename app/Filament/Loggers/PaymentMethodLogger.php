<?php

namespace App\Filament\Loggers;

use App\Enums\isEnabled;
use App\Filament\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class PaymentMethodLogger extends Logger
{
    public static ?string $model = PaymentMethod::class;

    public static function getLabel(): string | Htmlable | null
    {
        return PaymentMethodResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('name')
                    ->label('Name'),
                Field::make('is_enabled')
                    ->formatStateUsing(fn ($state) => $state == 1 ? 'True' : 'False')
                    ->label('Enabled'),
                Field::make('is_digital')
                    ->formatStateUsing(fn ($state) => $state == 1 ? 'True' : 'False')
                    ->label('Digital'),
                Field::make('logo')
                    ->media(gallery:true),
            ])
            ->relationManagers([
                //
            ]);
    }
}
