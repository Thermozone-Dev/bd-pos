<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Item;
use App\Models\Product;
use App\Models\ProductType;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Items & Discounts';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Product Name')
                    ->required(),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required(),
                Select::make('product_type_id')
                    ->label('Product Type')
                    ->options(
                        ProductType::query()
                            ->get()
                            ->mapWithKeys(fn ($type) => [$type->id => $type->name])
                            ->toArray(),
                    )
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                SpatieMediaLibraryFileUpload::make('image')
                    ->label('Product Image')
                    ->downloadable(),
                // Select::make('packages')
                //     ->relationship('packages', 'name')
                //     ->multiple()
                //     ->preload()
                //     ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->sortable(),
                TextColumn::make('productType.name')
                    ->sortable(),
                TextColumn::make('sku')
                    ->sortable(),
                SpatieMediaLibraryImageColumn::make('image'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('delete')
                    ->action(function (Model $record) {
                        $record->item()->get()->first()->delete();
                        $record->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->action(function (Collection $records) {
                            $records->each(function ($record){
                                $record->item()->get()->first()->delete();
                                $record->delete();
                            });
                        }),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
