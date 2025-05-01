<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BestEmployees extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->where('name' , '!=', '')

            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Earnings')
                    ->sortable()
            ]);
    }
}
