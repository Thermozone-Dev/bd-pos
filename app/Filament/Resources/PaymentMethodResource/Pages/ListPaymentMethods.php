<?php

namespace App\Filament\Resources\PaymentMethodResource\Pages;

use App\Filament\Resources\PaymentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentMethods extends ListRecords
{
    protected static string $resource = PaymentMethodResource::class;

    protected static ?string $title = 'Enable Payment Methods';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(true),
        ];
    }
}
