<?php

namespace App\Filament\Loggers;

use App\Models\Shift;
use App\Filament\Resources\ShiftResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class ShiftLogger extends Logger
{
    public static ?string $model = Shift::class;

    public static function getLabel(): string | Htmlable | null
    {
        return ShiftResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('time_in')
                    ->label('Time In'),
                Field::make('opening_balance')
                    ->label('Opening Balance'),
                Field::make('time_out')
                    ->label('Time Out'),
                Field::make('ending_balance')
                    ->label('Ending Balance'),
                Field::make('request-time')
                    ->label('Request Time'),
            ])
            ->relationManagers([
                //
            ]);
    }

    public function requestXReading(): Void
    {
        $attributes = [
            'request-time' => now()->toDateTimeLocalString(),
        ];

        $this->log(
            ["attributes" => $attributes],
            event: "request-x-reading",
        );
    }

    public function requestZReading(): Void
    {
        $attributes = [
            'request-time' => now()->toDateTimeLocalString(),
        ];

        $this->log(
            ["attributes" => $attributes],
            event: "request-z-reading",
        );
    }
}
