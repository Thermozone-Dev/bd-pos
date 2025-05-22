<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoidTransactionResource\Pages;
use App\Filament\Resources\VoidTransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\VoidTransaction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoidTransactionResource extends Resource
{
    protected static ?string $model = VoidTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('transaction_id')
                    ->label('Transaction ID')
                    ->option(
                        Transaction::query()
                        ->get()
                        ->mapWithKeys(fn ($transaction) => [$transaction->id => $transaction->name])
                        ->toArray(),
                    )
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')
                ->label('Transaction ID'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVoidTransactions::route('/'),
            'create' => Pages\CreateVoidTransaction::route('/create'),
            'edit' => Pages\EditVoidTransaction::route('/{record}/edit'),
        ];
    }
}
