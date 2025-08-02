<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\TransactionBasket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('processed_by')
                    ->options(
                        User::query()
                            ->get()
                            ->mapWithKeys(fn ($user) => [$user->id => $user->name])
                            ->toArray(),
                    )
                    ->required(),
                TextInput::make('barcode')
                    ->required(),
                Select::make('transaction_method_id')
                    ->label('Transaction Method')
                    ->options([
                        'cash' => 'Cash',
                        'credit_card' => 'Credit Card',
                        'debit_card' => 'Debit Card',
                        'gcash' => 'GCash',
                        'maya' => 'Maya',
                    ])
                    ->required(),
                TextInput::make('transaction_fee')
                    ->label('Transaction Fee')
                    ->numeric()
                    ->default('0'),
                TextInput::make('reference_number')
                    ->label('Reference Number')
                    ->nullable(),
                TextInput::make('vatable_sales')
                    ->label('VATable Sales')
                    ->numeric()
                    ->required()
                    ->default('0'),
                TextInput::make('cash_tendered')
                    ->label('Cash Tendered')
                    ->numeric()
                    ->required()
                    ->default('0'),
                TextInput::make('change')
                    ->label('Change')
                    ->numeric()
                    ->default('0')
                    ->required(),
                TextInput::make('vat')
                    ->label('VAT')
                    ->numeric()
                    ->default('0'),
                TextInput::make('vat_exempt_sales')
                    ->label('VAT Exempt Sales')
                    ->numeric()
                    ->default('0'),
                TextInput::make('zero_rated_sales')
                    ->label('Zero Rated Sales')
                    ->numeric()
                    ->default('0'),
                Toggle::make('is_valid')->label('Valid')->default(true),
                Toggle::make('is_pwd')->label('PWD'),
                Toggle::make('is_sc')->label('Senior Citizen'),
                Toggle::make('is_nac')->label('National Athlete / Coach'),
                Toggle::make('is_soloparent')->label('Solo Parent'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('processedBy.name')
                ->label('Processed By'),
                TextColumn::make('transaction_basket_id')
                    ->label('Transaction Basket ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime('M d, Y - h:i A')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('barcode')
                    ->label('Barcode')
                    ->formatStateUsing(fn ($state) => str_pad($state, 6, '0', STR_PAD_LEFT)),
                TextColumn::make('or_number')
                    ->label('OR Number')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paymentMethod.name')
                    ->label('Transaction Method'),
                TextColumn::make('transaction_fee')
                    ->label('Transaction Fee')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('reference_number')
                    ->label('Reference Number')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gross_sales')
                    ->label('Gross Sales')
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('cash_tendered')
                    ->label('Cash Tendered')
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('change')
                    ->label('Change')
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('vatable_sales')
                    ->label('VATable Sales')
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('vat')
                    ->label('VAT')
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('vat_exempt_sales')
                    ->label('VAT Exempt Sales')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('vat_deduction')
                    ->label('VAT Deduction')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('vat_adjustment')
                    ->label('VAT Adjustment')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('zero_rated_sales')
                    ->label('Zero Rated Sales')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('total_sales')
                    ->label('Total Sales')
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                IconColumn::make('is_valid')
                    ->label('Valid')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),
                IconColumn::make('is_zero_rated')
                    ->label('Zero Rated')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                IconColumn::make('is_pwd')
                    ->label('PWD')
                    ->label('PWD')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                IconColumn::make('is_sc')
                    ->label('Senior Citizen')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                IconColumn::make('is_nac')
                    ->label('National Athlete / Coach')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                IconColumn::make('is_soloparent')
                    ->label('Solo Parent')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('processed_by')
                ->label('Processed By')
                ->relationship('processedBy', 'name')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->when(auth()->user()->hasRole('Cashier'), function (Builder $query) {
                $query->where('processed_by', auth()->user()->id);
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
