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
                // Field::make('processed_by')
                //     ->label('Processed By'),
                Field::make('transaction_basket_id')
                    ->label('Transaction Basket ID'),
                // Field::make('barcode')
                //     ->label('Barcode'),
                Field::make('transaction_method_id')
                    ->label('Transaction Method ID'),
                // Field::make('transaction_fee')
                //     ->label('Transaction Fee'),
                // Field::make('cash_tendered')
                //     ->label('Cash Tendered'),
                // Field::make('change')
                //     ->label('Change'),
                // Field::make('gross_sales')
                //     ->label('Gross Sales'),
                // Field::make('vatable_sales')
                //     ->label('VATable Sales'),
                // Field::make('vat')
                //     ->label('VAT'),
                // Field::make('vat_exempt_sales')
                //     ->label('VAT Exempt Sales'),
                // Field::make('vat_exempt')
                //     ->label('VAT Exempt'),
                // Field::make('zero_rated_sales')
                //     ->label('Zero Rated Sales'),
                Field::make('total_sales')
                    ->label('Total Sales'),
                Field::make('is_valid')
                    ->label('Is Valid'),
                // Field::make('is_pwd')
                //     ->label('Is PWD'),
                // Field::make('is_sc')
                //     ->label('Is SC'),
                // Field::make('is_nac')
                //     ->label('Is NAC'),
                // Field::make('is_soloparent')
                //     ->label('Is Solo Parent'),
            ])
            ->relationManagers([
                //
            ]);
    }
}
