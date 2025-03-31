<?php

namespace App\Filament\Resources\PwdInfoResource\Pages;

use App\Filament\Resources\PwdInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPwdInfo extends ViewRecord
{
    protected static string $resource = PwdInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
