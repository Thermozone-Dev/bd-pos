<?php

namespace App\Filament\Resources\VoidTransactionResource\Pages;

use App\Filament\Resources\VoidTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVoidTransaction extends CreateRecord
{
    protected static string $resource = VoidTransactionResource::class;
}
