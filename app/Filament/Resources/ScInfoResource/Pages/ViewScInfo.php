<?php

namespace App\Filament\Resources\ScInfoResource\Pages;

use App\Filament\Resources\ScInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewScInfo extends ViewRecord
{
    protected static string $resource = ScInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
