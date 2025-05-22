<?php

namespace App\Filament\Resources\VoidTransactionResource\Pages;

use App\Filament\Resources\VoidTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVoidTransactions extends ListRecords
{
    protected static string $resource = VoidTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
