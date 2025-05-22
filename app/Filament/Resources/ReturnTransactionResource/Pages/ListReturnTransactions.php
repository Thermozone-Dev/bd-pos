<?php

namespace App\Filament\Resources\ReturnTransactionResource\Pages;

use App\Filament\Resources\ReturnTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReturnTransactions extends ListRecords
{
    protected static string $resource = ReturnTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
