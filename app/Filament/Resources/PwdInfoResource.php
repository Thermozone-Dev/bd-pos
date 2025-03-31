<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PwdInfoResource\Pages;
use App\Filament\Resources\PwdInfoResource\RelationManagers;
use App\Models\PwdInfo;
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

class PwdInfoResource extends Resource
{
    protected static ?string $model = PwdInfo::class;

    protected static ?string $modelLabel = 'PWD Transaction';

    protected static ?string $pluralModelLabel = 'PWD Transactions';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Persons with Disabilities';

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
                TextInput::make('pwd_id')->label('PWD ID'),
                TextInput::make('pwd_tin')->label('PWD TIN'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')->label('Transaction ID'),
                TextColumn::make('name')->label('PWD Name'),
                TextColumn::make('pwd_id')->label('PWD ID'),
                TextColumn::make('pwd_tin')->label('PWD TIN'),
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
            'index' => Pages\ListPwdInfos::route('/'),
            'create' => Pages\CreatePwdInfo::route('/create'),
            'view' => Pages\ViewPwdInfo::route('/{record}'),
            'edit' => Pages\EditPwdInfo::route('/{record}/edit'),
        ];
    }
}
