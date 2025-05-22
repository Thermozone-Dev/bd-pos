<?php

namespace App\Filament\Resources\VoidTransactionResource\Pages;

use App\Filament\Resources\VoidTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVoidTransaction extends EditRecord
{
    protected static string $resource = VoidTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
