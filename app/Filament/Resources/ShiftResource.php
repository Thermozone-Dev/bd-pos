<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftResource\Pages;
use App\Filament\Resources\ShiftResource\RelationManagers;
use App\Models\Shift;
use App\Models\User;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->options(
                        User::query()
                            ->get()
                            ->mapWithKeys(fn ($user) => [$user->id => $user->name])
                            ->toArray(),
                    )
                    ->columnSpanFull()
                    ->required(),
                DateTimePicker::make('time_in')
                    ->native(false)
                    ->required()
                    ->label('Date & Time In')
                    ->timezone('Asia/Manila')
                    ->seconds(false)
                    ->displayFormat('M d, Y - h:i A'),
                DateTimePicker::make('time_out')
                    ->native(false)
                    ->required()
                    ->label('Date & Time Out')
                    ->timezone('Asia/Manila')
                    ->seconds(false)
                    ->displayFormat('M d, Y - h:i A'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User'),
                TextColumn::make('time_in')
                    ->label('Time In')
                    ->timezone('Asia/Manila')
                    ->dateTime('M d, Y - h:i A'),
                TextColumn::make('time_out')
                    ->label('Time Out')
                    ->timezone('Asia/Manila')
                    ->dateTime('M d, Y - h:i A'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'view' => Pages\ViewShift::route('/{record}'),
            'edit' => Pages\EditShift::route('/{record}/edit'),
        ];
    }
}
