<?php

namespace App\Filament\Resources;

use App\Enums\ProductTaxCategory;
use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Item;
use App\Models\Package;
use App\Models\Product;
use Closure;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Items & Discounts';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Package Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Package Name')
                            ->columnSpan(1),
                        TextInput::make('price')->label('Price')
                            ->numeric()
                            ->minValue(0)
                            ->disabled()
                            ->readOnly()
                            ->live(onBlur: false, debounce: 500)
                            ->dehydrated()
                            ->columnSpan(1),

                        Select::make('product_tax_category')
                            ->label('Product Tax Category')
                            ->options(ProductTaxCategory::class)
                            ->default(ProductTaxCategory::OTHER)
                            ->required(),

                        TextInput::make('pax')
                            ->label('PAX')
                            ->default(0)
                            ->numeric(),

                        Section::make('Base Rate')
                            ->columns(2)
                            ->schema([
                                TextInput::make('base_rate_name')
                                    ->label('Base Rate Name')
                                    ->columnSpan(1),
                                TextInput::make('base_price')
                                    ->label('Base Price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->columnSpan(1)
                                    ->live(onBlur: false, debounce: 500)
                                    // ->mask(RawJs::make('$money($input)'))
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $basePrice = $get('base_price') ?? 0;
                                        $inclusiveItems = $get('packageHasPackageInclusive') ?? [];
                                        $inclusiveSum = collect($inclusiveItems)
                                            ->pluck('price')
                                            ->map(fn ($p) => (float) $p)
                                            ->sum();

                                        $set('price', $basePrice + $inclusiveSum);
                                    })
                                    ->placeholder(0)
                                    ->prefix('₱'),
                            ]),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->label('Product Image')
                            ->downloadable(),
                    ]),

                Section::make('Package Items')
                    ->schema([
                        Select::make('products')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->columnSpanFull()
                            ->preload()
                            ->searchable()
                            ->required(),
                    ]),

                    Repeater::make('packageHasPackageInclusive')
                        ->relationship()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $basePrice = $get('base_price') ?? 0;
                            $inclusiveItems = $get('packageHasPackageInclusive') ?? [];
                            $inclusiveSum = collect($inclusiveItems)
                                ->pluck('price')
                                ->map(fn ($p) => (float) $p)
                                ->sum();

                            $set('price', $basePrice + $inclusiveSum);
                        })
                        ->columnSpanFull()
                        ->schema([
                            Select::make('package_inclusive_id')
                                ->relationship(name: 'packageInclusive', titleAttribute: 'name')
                                ->columnSpan(2)
                                ->preload()
                                ->afterStateUpdated(function (callable $set, $state, Get $get) {
                                    if ($state) {
                                        $packageInclusive = \App\Models\PackageInclusive::find($state);
                                        if ($packageInclusive) {
                                            $set('price', $packageInclusive->price);
                                        }
                                    } else {
                                        $set('price', 0);
                                    }

                                    $basePrice = (float) $get('../../base_price'); // navigate out of the repeater
                                    $inclusivePrices = $get('../../packageHasPackageInclusive') ?? [];

                                    $inclusiveSum = collect($inclusivePrices)
                                        ->pluck('price')
                                        ->map(fn ($p) => (float) $p)
                                        ->sum();

                                    $set('../../price', $basePrice + $inclusiveSum);

                                })
                                ->live(onBlur: false, debounce: 500)
                                ->searchable()
                                ->required(),

                            TextInput::make('price')->label('Price')
                                ->required()
                                ->numeric()
                                ->live(onBlur: false, debounce: 500)
                                ->afterStateUpdated(function (Get $get, Set $set) {

                                    $basePrice = (float) $get('../../base_price'); // navigate out of the repeater
                                    $inclusivePrices = $get('../../packageHasPackageInclusive') ?? [];
                                    $inclusiveSum = collect($inclusivePrices)
                                        ->pluck('price')
                                        ->map(fn ($p) => (float) $p)
                                        ->sum();

                                    $set('../../price', $basePrice + $inclusiveSum);
                                })
                                ->placeholder(0)
                                ->minValue(1)
                                ->live()
                                ->prefix('₱'),
                        ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->label('Image')
                    ->defaultImageUrl(fn () => asset('image/pos-default.jpg')),
                TextColumn::make('name')->label('Package Name'),
                TextColumn::make('price')->label('Price'),
                TextColumn::make('pax')->label('PAX')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : 'N/A'),
                TextColumn::make('product_tax_category')->label('Product Tax Category'),
                TextColumn::make('products.name')->label('Products')->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('delete')
                    ->action(function (Model $record) {
                        foreach ($record->productsJunction()->get() as $row) {
                            $row->delete();
                        }
                        $record->item()->get()->first()->delete();
                        $record->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->action(function (Collection $records) {
                            $records->each(function ($record){
                                foreach ($record->productsJunction()->get() as $row) {
                                    $row->delete();
                                }
                                $record->item()->get()->first()->delete();
                                $record->delete();
                            });
                        }),
                ]),
            ]);
    }


    public static function query(EloquentBuilder $query): EloquentBuilder
    {
        return $query->with('products');
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'view' => Pages\ViewPackage::route('/{record}'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
