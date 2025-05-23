<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

use function Laravel\Prompts\form;

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
                        ->required(),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data) {

                    // Test for shifts
                    $shift = auth()->user()->shifts()->latest()->get()->first();
                    $time_in = $shift->time_in;
                    $beginningOR = Transaction::query()
                        ->where('created_at', '>=', $time_in)
                        ->orderBy('created_at')
                        ->get()
                        ->first()
                        ->id;
                    $endingOR = Transaction::query()
                        ->where('created_at', '>=', $time_in)
                        ->orderBy('created_at')
                        ->get()
                        ->last()
                        ->id;
                    $openingFund = $shift->opening_balance;
                    $totalCashPayment = Transaction::query()
                        ->where('created_at', '>=', $time_in)
                        ->where('transaction_method_id', '1')
                        ->sum('cash_tendered');
                    $totalDigitalPayment = Transaction::query()
                        ->where('created_at', '>=', $time_in)
                        ->where('transaction_method_id', '!=', '1' )
                        ->Where('transaction_method_id', '!=', '5' )
                        ->sum('cash_tendered');
                    $totalCreditPayment = Transaction::query()
                        ->where('created_at', '>=', $time_in)
                        ->where('transaction_method_id', '5')
                        ->sum('cash_tendered');
                    $totalPayments = Transaction::query()
                        ->where('created_at', '>=', $time_in)
                        ->sum('cash_tendered');
                    $voidValue = Transaction::query()
                        ->where('created_at', '>=', $time_in)
                        ->where('is_valid', false)
                        ->sum('cash_tendered');
                    $refundValue = Transaction::query()
                        ->where('created_at', '>=', $time_in)
                        ->where('is_valid', false)
                        ->sum('cash_tendered');

                    dd($voidValue);

                    $filteredTransactions = Transaction::with('basket.items')
                        ->whereBetween('created_at', [$data['start_date'], $data['end_date']])
                        ->get();

                    $dailyDiscounts = $filteredTransactions
                        ->groupBy(fn($transaction) => $transaction->created_at->toDateString())
                        ->map(function ($transactionsOfDay) {
                            $dayTotal = 0;

                            foreach ($transactionsOfDay as $transaction) {
                                if ($transaction->basket) {
                                    $dayTotal += $transaction->basket->items->sum('discount_value');
                                }
                            }

                            return $dayTotal;
                        });

                    $transactions = Transaction::query()
                        ->selectRaw('DATE(created_at) as date')
                        ->selectRaw('MIN(id) as beginningOR')
                        ->selectRaw('MAX(id) as endingOR')
                        // ->selectRaw('MAX(id) as grandBeginningBal')
                        // ->selectRaw('MAX(id) as grandEndingBal')
                        ->selectRaw('SUM(gross_sales) as grossSales')
                        ->selectRaw('SUM(total_sales) as totalSales')
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

                    $pdf = SnappyPdf::loadView('reports.bir-summary', [
                        'transactions' => $transactions,
                        'discounts' => $dailyDiscounts,
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('BIR Summary Report.pdf');

                    // return response()->streamDownload(
                    //     fn () => print($pdf->output()),
                    //     'document.pdf'
                    // );
                }),
            Action::make('downloadGeneral ')
                ->label('General Transaction Summary Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required(),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->default(now()),
                ])
                ->action(function(array $data) {

                     $transactions = Transaction::query()
                        ->whereBetween('created_at', [$data['start_date'], $data['end_date']])
                        ->get();


                    $pdf = SnappyPdf::loadView('reports.general-transaction-summary', [
                        'transactions' => $transactions,
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('General Transaction Summary Report.pdf');

                }),
        ];
    }
}
