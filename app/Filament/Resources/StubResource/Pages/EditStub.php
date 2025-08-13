<?php

namespace App\Filament\Resources\StubResource\Pages;

use App\Filament\Resources\StubResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStub extends EditRecord
{
    protected static string $resource = StubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
