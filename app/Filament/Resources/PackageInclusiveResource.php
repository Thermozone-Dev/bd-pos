<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageInclusiveResource\Pages;
use App\Filament\Resources\PackageInclusiveResource\RelationManagers;
use App\Models\PackageInclusive;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageInclusiveResource extends Resource
{
    protected static ?string $model = PackageInclusive::class;



protected static ?string $navigationIcon = 'heroicon-o-gift-top';

protected static ?string $navigationGroup = 'Items & Discounts';

protected static ?int $navigationSort = 8;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('stall_id')
                //     ->numeric(),
                Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->columnSpan(2)
                            ->maxLength(100),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->placeholder(0)
                            ->minValue(1)
                            ->prefix('₱'),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->hiddenOn(['view','create'])
                    ->required(),

                Repeater::make('packageInclusiveProducts')
                    ->label('Package Inclusives Items')
                    ->relationship()
                    ->defaultItems(0)
                    ->schema([
                        Grid::make(3)
                        ->schema([
                            Select::make('product_id')
                                ->relationship(name: 'product', titleAttribute: 'name')
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->columnSpan(2)
                                ->required(),
                            TextInput::make('qty')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->maxValue(100)
                                ->required(),
                        ])
                    ])
                    ->columns(2)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('stall_id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPackageInclusives::route('/'),
            'create' => Pages\CreatePackageInclusive::route('/create'),
            'view' => Pages\ViewPackageInclusive::route('/{record}'),
            'edit' => Pages\EditPackageInclusive::route('/{record}/edit'),
        ];
    }
}
