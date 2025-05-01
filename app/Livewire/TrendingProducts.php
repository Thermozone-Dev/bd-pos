<?php

namespace App\Livewire;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TrendingProducts extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()->where('price' , '>', 0)
                    ->orderBy('price', 'desc')
            )
            ->columns([
                SpatieMediaLibraryImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Orders')
                    ->sortable()
            ]);
    }
}
