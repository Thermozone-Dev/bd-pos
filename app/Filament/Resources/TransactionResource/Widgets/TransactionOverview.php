<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use App\Models\TransactionBasket;
use App\Models\TransactionBasketItem;
use Closure;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
class TransactionOverview extends BaseWidget
{

    public ?Model $record = null;

    protected int | string | array $columnSpan = 'full';


    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransactionBasketItem::query()->where('transaction_basket_id', $this->record->transaction_basket_id)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Item Name')
                    ->formatStateUsing(function ($state) {
                        $item = TransactionBasketItem::findOrFail($state)->item;
                        if($item->package_id ?? false){ // package
                            $text =  $item->package->name;
                        }else{
                             $text =  $item->product->name;
                        }
                        return $text;
                    }),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric(),
                TextColumn::make('item')
                    ->label('Price')
                    ->numeric()
                    ->formatStateUsing(function ($state) {
                        if($state->package_id){ // package
                            $price =  $state->package->price;
                        }else{
                             $price =  $state->product->price;
                        }
                        return number_format($price, 2);
                    }),
                TextColumn::make('total_value')
                    ->label('Total Price')
                    ->prefix('₱ ')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2)),
            ]);
    }
}
