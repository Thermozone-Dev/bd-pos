<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScInfoResource\Pages;
use App\Filament\Resources\ScInfoResource\RelationManagers;
use App\Models\ScInfo;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScInfoResource extends Resource
{
    protected static ?string $model = ScInfo::class;

    protected static ?string $modelLabel = 'SC Transaction';

    protected static ?string $pluralModelLabel = 'SC Transactions';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Senior Citizens';

    protected static ?string $navigationGroup = 'Discount Reports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('transaction_id')
                    ->options(
                        Transaction::query()
                            ->get()
                            ->mapWithKeys(fn ($transaction) => [$transaction->id => $transaction->id])
                            ->toArray(),
                    )
                    ->label('Transaction ID')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('sc_id')->label('SC ID'),
                TextInput::make('sc_tin')->label('SC TIN'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')->label('Transaction ID'),
                TextColumn::make('name')->label('SC Name'),
                TextColumn::make('sc_id')->label('SC ID'),
                TextColumn::make('sc_tin')->label('SC TIN'),
                TextColumn::make('created_at')
                    ->timezone('Asia/Manila')
                    ->dateTime('M d, Y - h:i A'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListScInfos::route('/'),
            'create' => Pages\CreateScInfo::route('/create'),
            'view' => Pages\ViewScInfo::route('/{record}'),
            'edit' => Pages\EditScInfo::route('/{record}/edit'),
        ];
    }
}
