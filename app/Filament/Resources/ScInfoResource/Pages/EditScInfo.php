<?php

namespace App\Filament\Resources\ScInfoResource\Pages;

use App\Filament\Resources\ScInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScInfo extends EditRecord
{
    protected static string $resource = ScInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
