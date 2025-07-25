<?php

namespace App\Filament\Loggers;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class TransactionLogger extends Logger
{
    public static ?string $model = Transaction::class;

    public static function getLabel(): string | Htmlable | null
    {
        return TransactionResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('transaction_basket_id')
                    ->label('Transaction Basket ID'),
                Field::make('transaction_method_id')
                    ->label('Transaction Method ID'),
                Field::make('total_sales')
                    ->label('Total Sales'),
                Field::make('is_valid')
                    ->formatStateUsing(fn ($state) => $state == 1 ? 'True' : 'False')
                    ->label('Is Valid'),
            ])
            ->relationManagers([
                //
            ]);
    }

}
