<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftResource\Pages;
use App\Filament\Resources\ShiftResource\RelationManagers;
use App\Models\Shift;
use App\Models\User;
use Closure;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-up';

    protected static ?int $navigationSort = 9;

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
                SelectFilter::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->searchable()
                ->preload(),

                Filter::make('created_at')
                ->form([
                    DatePicker::make('from')->label('From Date'),
                    DatePicker::make('until')->label('To Date'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                }),

                Filter::make('search_filters')
                    ->form([
                        Grid::make()
                            ->schema([
                                Toggle::make('filter_date_by_range')
                                    ->label(fn ($state) => $state ? 'Turn off to filter by date range' : 'Turn on to filter by specific date')
                                    ->default(false)
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('date_from', null);
                                        $set('date_to', null);
                                        $set('date', null);
                                    })
                                    ->inline(false)
                                    ->reactive(),
                                Grid::make(2)
                                    ->hidden(fn (Get $get) => $get('filter_date_by_range') === true)
                                    ->schema([
                                        TextInput::make('date_from')->numeric(),
                                        TextInput::make('date_to')->numeric(),
                                    ])
                                    ->columnSpan(2),
                                Grid::make(1)
                                    ->hidden(fn (Get $get) => $get('filter_date_by_range') === false)
                                    ->schema([
                                        TextInput::make('date')->label('Date'),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(isset($data['date']) && $data['filter_date_by_range'], fn ($query) =>
                                $query->whereDate('created_at', $data['date'])
                            )
                            ->when(
                                isset($data['date_from'], $data['date_to']) && !$data['filter_date_by_range'],
                                fn ($query) => $query->whereBetween('created_at', [$data['date_from'], $data['date_to']])
                            );
                    })
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
