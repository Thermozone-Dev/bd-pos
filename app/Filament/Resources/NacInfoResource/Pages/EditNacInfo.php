<?php

namespace App\Filament\Resources\NacInfoResource\Pages;

use App\Filament\Resources\NacInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNacInfo extends EditRecord
{
    protected static string $resource = NacInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
