<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Exports\TransactionExport;
use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

use function Laravel\Prompts\form;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('downloadPdf')
            //     ->label('Generate BIR Summary Report PDF')
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->form([
            //         DatePicker::make('start_date')
            //             ->label('Start Date')
            //             ->required(),
            //         DatePicker::make('end_date')
            //             ->label('End Date')
            //             ->required()
            //             ->default(now()),
            //     ])
            //     ->action(function (array $data) {

            //         $filteredTransactions = Transaction::with('basket.items')
            //             ->whereBetween('created_at', [$data['start_date'], $data['end_date']])
            //             ->get();

            //         $dailyDiscounts = $filteredTransactions
            //             ->groupBy(fn($transaction) => $transaction->created_at->toDateString())
            //             ->map(function ($transactionsOfDay) {
            //                 $dayTotal = 0;

            //                 foreach ($transactionsOfDay as $transaction) {
            //                     if ($transaction->basket) {
            //                         $dayTotal += $transaction->basket->items->sum('discount_value');
            //                     }
            //                 }

            //                 return $dayTotal;
            //             });

            //         $transactions = Transaction::query()
            //             ->selectRaw('DATE(created_at) as date')
            //             ->selectRaw('MIN(id) as beginningOR')
            //             ->selectRaw('MAX(id) as endingOR')
            //             // ->selectRaw('MAX(id) as grandBeginningBal')
            //             // ->selectRaw('MAX(id) as grandEndingBal')
            //             ->selectRaw('SUM(gross_sales) as grossSales')
            //             ->selectRaw('SUM(total_sales) as totalSales')
            //             ->selectRaw('SUM(vatable_sales) as vatableSales')
            //             ->selectRaw('SUM(vat) as vat')
            //             ->selectRaw('SUM(vat_exempt_sales) as vatExemptSales')
            //             ->selectRaw('SUM(vat_exempt) as vatExempt')
            //             ->selectRaw('SUM(zero_rated_sales) as zeroRatedSales')
            //             ->where('is_valid', true)
            //             ->whereBetween('created_at', [$data['start_date'], $data['end_date']])
            //             ->groupByRaw('DATE(created_at)')
            //             ->orderByRaw('DATE(created_at)')
            //             ->get();

            //         // dd($transactions);

            //         $pdf = SnappyPdf::loadView('reports.bir-summary', [
            //             'transactions' => $transactions,
            //             'discounts' => $dailyDiscounts,
            //         ])->setPaper('folio', 'landscape');

            //         return $pdf->stream('BIR Summary Report.pdf');

            //         // return response()->streamDownload(
            //         //     fn () => print($pdf->output()),
            //         //     'document.pdf'
            //         // );
            //     }),
            Action::make('downloadGeneral')
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

                    $report = $this->export_value($data);

                    $pdf = SnappyPdf::loadView('reports.general-transaction-summary', [
                        'transactions' => $report['transactions'],
                        'discountSummary' => $report['discountSummary'],
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('General Transaction Summary Report.pdf');

                }),

                Action::make('export_record_to_excel')
                    ->label('Export Record to Excel')
                    ->icon('fas-file-export')
                    ->color('success')
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
                        $report = $this->export_value($data);

                        $export = new TransactionExport(
                            $report, //variable
                            'general_transaction' // blade path of export table
                        );
                        return Excel::download($export, 'General Transaction Summary Report.xlsx');

                    }),
        ];
    }

    public function export_value($data): array
    {

        $transactions = Transaction::query()
            ->whereBetween('created_at', [Carbon::parse($data['start_date'])->startOfDay() , Carbon::parse($data['end_date'])->endOfDay()])
            ->get();

        $discountSummary = [];  

        foreach ($transactions as $transaction) {
            // Initialize counters
            $discounts = [
                'sc' => 0,
                'pwd' => 0,
                'nac' => 0,
                'solo_parent' => 0,
            ];

            $basket = $transaction->basket()->first();
            if (!$basket) continue;

            $items = $basket->items()->get();

            foreach ($items as $item) {
                // Skip if no discount
                if (empty($item->discount_value) || $item->discount_value == 0.00) {
                    continue;
                }

                // dump($item->discount_value, $item->discounts()->first()->discount_id);

                // Add to correct category based on discount_id
                switch ($item->discounts()->first()->discount_id) {
                    case 1:
                        $discounts['sc'] += $item->discount_value;
                        break;
                    case 2:
                        $discounts['pwd'] += $item->discount_value;
                        break;
                    case 3:
                        $discounts['nac'] += $item->discount_value;
                        break;
                    case 4:
                        $discounts['solo_parent'] += $item->discount_value;
                        break;
                }
            }

            $discountSummary[$transaction->id] = $discounts;
        }

        return [
            'transactions' => $transactions,
            'discountSummary' => $discountSummary,
        ];
    }
}
