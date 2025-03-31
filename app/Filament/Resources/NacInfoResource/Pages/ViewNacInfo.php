<?php

namespace App\Filament\Resources\NacInfoResource\Pages;

use App\Filament\Resources\NacInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNacInfo extends ViewRecord
{
    protected static string $resource = NacInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
