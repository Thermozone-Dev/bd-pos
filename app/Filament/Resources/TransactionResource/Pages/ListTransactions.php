<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Generate BIR Summary Report PDF')
                ->icon('heroicon-o-arrow-down-tray')
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

                    $transactions = Transaction::query()
                        ->selectRaw('DATE(created_at) as date')
                        ->selectRaw('MIN(id) as beginningOR')
                        ->selectRaw('MAX(id) as endingOR')
                        // ->selectRaw('MAX(id) as grandBeginningBal')
                        // ->selectRaw('MAX(id) as grandEndingBal')
                        ->selectRaw('SUM(gross_sales) as grossSales')
                        ->selectRaw('SUM(vatable_sales) as vatableSales')
                        ->selectRaw('SUM(vat) as vat')
                        ->selectRaw('SUM(vat_exempt_sales) as vatExemptSales')
                        ->selectRaw('SUM(vat_exempt) as vatExempt')
                        ->selectRaw('SUM(zero_rated_sales) as zeroRatedSales')
                        ->where('is_valid', true)
                        ->whereBetween('created_at', [$data['start_date'], $data['end_date']])
                        ->groupByRaw('DATE(created_at)')
                        ->orderByRaw('DATE(created_at)')
                        ->get();

                    // dd($transactions);

                    $pdf = SnappyPdf::loadView('reports.bir-summary')
                        ->setPaper('folio', 'landscape');

                    return $pdf->stream('BIR Summary Report.pdf');

                    // return response()->streamDownload(
                    //     fn () => print($pdf->output()),
                    //     'document.pdf'
                    // );
                }),
        ];
    }
}
