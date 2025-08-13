<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StubResource\Pages;
use App\Filament\Resources\StubResource\RelationManagers;
use App\Models\Stub;
use BladeUI\Icons\Components\Icon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StubResource extends Resource
{
    protected static ?string $model = Stub::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('transaction_id')
                    ->label('Transaction ID')
                    ->formatStateUsing(fn ($state) => str_pad($state, 6, '0', STR_PAD_LEFT))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('package_inclusive_id')
                    ->label('Inclusive ID')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('status')
                    ->options([
                        '0' => 'Unclaimed',
                        '1' => 'Claimed',
                    ])
                    ->default('unclaimed')
                    ->required(),
                Forms\Components\TextInput::make('stub_no')
                    ->label('Stub Number')
                    ->required()
                    ->maxLength(255)
                    ->default('08032025-140149477'),
                Forms\Components\TextInput::make('created_by')
                    ->label('Created By')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => str_pad($state, 6, '0', STR_PAD_LEFT))
                    ->sortable(),
                Tables\Columns\TextColumn::make('packageInclusive.name')
                    ->label('Inclusive')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stub_no')
                    ->label('Stub Number')
                    ->searchable(),
                IconColumn::make('status')
                    ->label('Claimed')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime()
                    ->sortable()
                    ->dateTime('M d, Y - h:i A'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListStubs::route('/'),
            'create' => Pages\CreateStub::route('/create'),
            'view' => Pages\ViewStub::route('/{record}'),
            'edit' => Pages\EditStub::route('/{record}/edit'),
        ];
    }
}
