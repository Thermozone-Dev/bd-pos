<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->placeholder('Juan Dela Cruz'),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('juan.delacruz@gmail.com'),
                        TextInput::make('password')
                            ->label('Password')
                            ->required()
                            ->password()
                            ->placeholder('********')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->visible(fn ($livewire) => $livewire instanceof CreateUser)
                            ->rule(Password::default()),
                        TextInput::make('confirm_password')
                            ->label('Confirm Password')
                            ->requiredWith('password')
                            ->password()
                            ->same('password')
                            ->placeholder('********')
                            ->visible(fn ($livewire) => $livewire instanceof CreateUser)
                            ->rule(Password::default()),
                ]),

                Section::make('New Password')
                    ->columns(2)
                    ->schema([
                        TextInput::make('new_password')
                            ->label('New Password')
                            ->nullable()
                            ->password()
                            ->placeholder('********')
                            ->visible(fn ($livewire) => $livewire instanceof EditUser)
                            ->rule(Password::default()),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->same('new_password')
                            ->requiredWith('new_password')
                            ->placeholder('********')
                            ->visible(fn ($livewire) => $livewire instanceof EditUser)
                            ->rule(Password::default()),
                ])->visible(fn ($livewire) => $livewire instanceof EditUser),

                Section::make('Authentication')
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->disableOptionWhen(fn(string $value): bool => auth()->user()->hasRole('super_admin') ? false : $value == 1),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('email')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('F j, Y - g:i A')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime('F j, Y - g:i A')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
