<?php

namespace App\Filament\Resources\SoloparentInfoResource\Pages;

use App\Filament\Resources\SoloparentInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoloparentInfo extends EditRecord
{
    protected static string $resource = SoloparentInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
