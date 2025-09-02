<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoloparentInfoResource\Pages;
use App\Filament\Resources\SoloparentInfoResource\RelationManagers;
use App\Models\SoloparentInfo;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SoloparentInfoResource extends Resource
{
    protected static ?string $model = SoloparentInfo::class;

    protected static ?string $modelLabel = 'Solo Parent Transaction';

    protected static ?string $pluralModelLabel = 'Solo Parent Transactions';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Solo Parents';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 6;

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
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('spic_id')
                    ->label('SPIC ID')
                    ->required(),
                Grid::make(3)
                    ->schema([
                        TextInput::make('child_name')
                            ->label("Child's Name"),
                        DatePicker::make('child_birthday')
                            ->label("Child's Birthday")
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) =>
                                $set('child_age', \Carbon\Carbon::parse($state)->age)
                            ),
                        TextInput::make('child_age')
                            ->label("Child's Age")
                            ->numeric(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_id')->label('Transaction ID')->formatStateUsing(fn ($state) => str_pad($state, 12, '0', STR_PAD_LEFT)),
                TextColumn::make('name')->label('Solo Parent Name'),
                TextColumn::make('spic_id')->label('SPIC ID'),
                TextColumn::make('child_name')->label('Child\'s Name'),
                TextColumn::make('child_birthday')->label('Child\'s Birthday'),
                TextColumn::make('child_age')->label('Child\'s Age'),
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
            'index' => Pages\ListSoloparentInfos::route('/'),
            'create' => Pages\CreateSoloparentInfo::route('/create'),
            'view' => Pages\ViewSoloparentInfo::route('/{record}'),
            'edit' => Pages\EditSoloparentInfo::route('/{record}/edit'),
        ];
    }
}
