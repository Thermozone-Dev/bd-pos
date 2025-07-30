<?php

namespace App\Filament\Resources\SoloparentInfoResource\Pages;

use App\Filament\Resources\SoloparentInfoResource;
use App\Models\SoloparentInfo;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

class ListSoloparentInfos extends ListRecords
{
    protected static string $resource = SoloparentInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('Download BIR E-5 Report PDF')
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

                    $spInfos = SoloparentInfo::query()
                        ->whereBetween('created_at', [Carbon::parse($data['start_date'])->startOfDay() , Carbon::parse($data['end_date'])->endOfDay()])
                        ->get();

                    $transactionIds = $spInfos->pluck('transaction_id')->unique();

                    $spTransactions = Transaction::whereIn('id', $transactionIds)
                        ->with('basket.items')
                        ->get();

                    $transactionDiscounts = [];

                    foreach ($spTransactions as $transaction) {
                        $totalDiscount = 0;

                        foreach ($transaction->basket() as $basket) {
                            foreach ($basket->items as $item) {
                                $totalDiscount += $item->discount_value ?? 0;
                            }
                        }

                        $transactionDiscounts[$transaction->transaction_basket_id] = $totalDiscount;
                    }

                    $pdf = SnappyPdf::loadView('reports.sp-summary', [
                        'spInfos' => $spInfos,
                        'spTransactions' => $spTransactions,
                        'transactionDiscounts' => $transactionDiscounts,
                    ])
                        ->setPaper('folio', 'landscape');

                    return $pdf->stream('E-5 - Solo Parent Report.pdf');
                }),
        ];
    }
}
