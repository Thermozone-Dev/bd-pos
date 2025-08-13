<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionSummaryDataResource\Pages;
use App\Filament\Resources\TransactionSummaryDataResource\RelationManagers;
use App\Models\Transaction;
use App\Models\TransactionSummaryData;
use App\Models\User;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class TransactionSummaryDataResource extends Resource
{
    protected static ?string $model = TransactionSummaryData::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.product-summary';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Product Summary';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'product-summary';

    // protected function getTableQuery(): Builder
    // {
    //     $query = TransactionSummaryData::query()
    //                 ->sum('qty')
    //                 ->sum('total')
    //                 ->orderBy('name')
    //                 ->groupBy('name');
    //     dd($query);
    //     return $query;
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                TransactionSummaryData::query()
                    ->select('name', 'price', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(total) as total_income'))
                    ->groupBy('name')
                    ->orderBy('name')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Product Name')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Price'),
                TextColumn::make('total_qty')
                    ->label('Quantity Sold')
                    ->sortable(),
                TextColumn::make('total_income')
                    ->label('Total Income')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable(),
                TextColumn::make('processed_by')
                    ->label('Processed By')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('processed_by')
                    ->label('Processed By')
                    ->options(
                        function () {
                            if (auth()->user()->cannot('viewAny', User::class)) {
                                return auth()->user()->name;
                            }
                            return User::all()->pluck('name', 'id');
                        }
                    )
                    ->default(
                        fn () =>
                            auth()->user()->hasRole('cashier') ?
                                auth()->user()->id : null
                    ),
                Filter::make('created_at')
                    ->label('Date')
                    ->form([
                        DatePicker::make('date')
                            ->label('Select Date')
                            ->default(now())
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['date'])) {
                            $query->whereDate('created_at', $data['date'] );
                        }
                        return $query;
                    }),
            ])
            ->persistFiltersInSession()
            ->deferFilters()
            ->actions([
                // Define any actions if needed
            ])
            ->bulkActions([
                // Define any bulk actions if needed
            ])
            ->defaultSort('name', 'desc');

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
            'index' => Pages\ListTransactionSummaryData::route('/'),
        ];
    }
}
