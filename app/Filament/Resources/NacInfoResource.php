<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NacInfoResource\Pages;
use App\Filament\Resources\NacInfoResource\RelationManagers;
use App\Models\NacInfo;
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

class NacInfoResource extends Resource
{
    protected static ?string $model = NacInfo::class;

    protected static ?string $modelLabel = 'NAC Transaction';

    protected static ?string $pluralModelLabel = 'NAC Transactions';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'National Athletes & Coaches';

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
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('pnstm_id')->label('PNSTM ID'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')->label('Transaction ID'),
                TextColumn::make('name')->label('NAC Name'),
                TextColumn::make('pnstm_id')->label('PNSTM ID'),
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
            'index' => Pages\ListNacInfos::route('/'),
            'create' => Pages\CreateNacInfo::route('/create'),
            'view' => Pages\ViewNacInfo::route('/{record}'),
            'edit' => Pages\EditNacInfo::route('/{record}/edit'),
        ];
    }
}
