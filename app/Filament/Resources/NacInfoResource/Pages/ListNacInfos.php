<?php

namespace App\Filament\Resources\NacInfoResource\Pages;

use App\Filament\Resources\NacInfoResource;
use App\Models\NacInfo;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

class ListNacInfos extends ListRecords
{
    protected static string $resource = NacInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('Download BIR E-4 Report PDF')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->default(now()->endOfDay()),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->default(now()->endOfWeek()),
                ])
                ->action(function (array $data) {

                    $nacInfos = NacInfo::query()
                        ->whereBetween('created_at', [$data['start_date'], $data['end_date']])
                        ->get();

                    $transactionIds = $nacInfos->pluck('transaction_id')->unique();

                    $nacTransactions = Transaction::whereIn('id', $transactionIds)
                        ->with('basket.items')
                        ->get();

                    $transactionDiscounts = [];

                    foreach ($nacTransactions as $transaction) {
                        $totalDiscount = 0;

                        foreach ($transaction->basket() as $basket) {
                            foreach ($basket->items as $item) {
                                $totalDiscount += $item->discount_value ?? 0;
                            }
                        }

                        $transactionDiscounts[$transaction->transaction_basket_id] = $totalDiscount;
                    }

                    $pdf = SnappyPdf::loadView('reports.nac-summary', [
                        'nacInfos' => $nacInfos,
                        'nacTransactions' => $nacTransactions,
                        'transactionDiscounts' => $transactionDiscounts,
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('E-4 - National Athletes and Coaches Report.pdf');
                }),
        ];
    }
}
