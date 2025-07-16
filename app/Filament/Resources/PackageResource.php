<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Item;
use App\Models\Package;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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
                            ->columnSpan(1),
                        TextInput::make('pax')
                            ->label('PAX')
                            ->numeric(),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->label('Image'),
                TextColumn::make('name')->label('Package Name'),
                TextColumn::make('price')->label('Price'),
                TextColumn::make('pax')->label('PAX')
                    ->formatStateUsing(fn ($state) => $state ?? 'N/A'),
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
