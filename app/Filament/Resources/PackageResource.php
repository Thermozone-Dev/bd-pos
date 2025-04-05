<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
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
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                TextColumn::make('products.name')->label('Products')->badge(),
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

    protected function handleRecordCreation(array $data): Model
    {
        $package = Package::create([
            'name' => $data['name'],
            'price' => $data['price'],
        ]);

        $package->products()->sync($data['products']);

        return $package;
    }

    protected function handleRecordUpdate(Model $package, array $data): Model
    {
        $package->update([
            'name' => $data['name'],
            'price' => $data['price'],
        ]);

        // Sync updated products
        $package->products()->sync($data['products']);

        return $package;
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
