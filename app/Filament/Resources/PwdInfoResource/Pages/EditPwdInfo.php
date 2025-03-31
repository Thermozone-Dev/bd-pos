<?php

namespace App\Filament\Resources\PwdInfoResource\Pages;

use App\Filament\Resources\PwdInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPwdInfo extends EditRecord
{
    protected static string $resource = PwdInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
