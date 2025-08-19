<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Exports\TransactionExport;
use App\Filament\Resources\TransactionResource;
use App\Journal\Journal;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use function Laravel\Prompts\form;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadJournal')
                ->label('Download E Journal')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(
                    function () {
                        if (Storage::disk('public')->exists(Journal::getJournalPath())){
                            Storage::download(storage_path(Journal::getJournalPath()), 'eJournal.txt');
                        }
                        else {
                            dd('NO FILE');
                        }
                    }
                ),

            Action::make('downloadPdf')
                ->label('Generate BIR Summary Report PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->default(now()),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data) {

                    $filteredTransactions = Transaction::with('basket.items')
                        ->whereBetween('created_at', [$data['start_date'], $data['end_date']])
                        ->get();

                    $dailyRelationalData = $filteredTransactions
                        ->groupBy(fn($transaction) => $transaction->created_at->toDateString())
                        ->map(function ($transactionsOfDay) {
                            $deductions = [
                                'sc' => 0,
                                'pwd' => 0,
                                'nac' => 0,
                                'solo_parent' => 0,
                                'others' => 0,
                                'returns' => 0,
                                'voids' => 0,
                                'day_total' => 0,
                            ];

                            $adjustments = [
                                'sc' => 0,
                                'pwd' => 0,
                                'other_discounts' => 0,
                                'returns' => 0,
                                'others' => 0,
                                'day_total' => 0,
                            ];

                            foreach ($transactionsOfDay as $transaction) {
                                if ($transaction->basket) {
                                    if ($transaction->is_sc) {
                                        $deductions['sc'] += $transaction->basket->items->sum('discount_value');
                                        $adjustments['sc'] += $transaction->sum('vat_adjustment');
                                    }
                                    else if ($transaction->is_pwd) {
                                        $deductions['pwd'] += $transaction->basket->items->sum('discount_value');
                                        $adjustments['pwd'] += $transaction->sum('vat_adjustment');
                                    }
                                    else if ($transaction->is_nac) {
                                        $deductions['nac'] += $transaction->basket->items->sum('discount_value');
                                        $adjustments['other_discounts'] += $transaction->sum('vat_adjustment');
                                    }
                                    else if ($transaction->is_soloparent) {
                                        $deductions['solo_parent'] += $transaction->basket->items->sum('discount_value');
                                        $adjustments['other_discounts'] += $transaction->sum('vat_adjustment');
                                    }
                                    else if(!$transaction->is_valid) {
                                        $deductions['voids'] += $transaction->total_sales;
                                        $adjustments['others'] += $transaction->sum('vat_adjustment');
                                    }
                                    else if ($transaction->basket->items->sum('discount_value') > 0) {
                                        $deductions['others'] += $transaction->basket->items->sum('discount_value');
                                        $adjustments['other_discounts'] += $transaction->sum('vat_adjustment');
                                    }
                                }
                            }

                            $deductions['day_total'] += $deductions['sc'] + $deductions['pwd'] + $deductions['nac'] + $deductions['solo_parent'] + $deductions['others'] + $deductions['returns'] + $deductions['voids'];
                            $adjustments['day_total'] += $adjustments['sc'] + $adjustments['pwd'] + $adjustments['other_discounts'] + $adjustments['returns'] + $adjustments['others'];

                            return [
                                'deductions' => $deductions,
                                'adjustments' => $adjustments,
                            ];
                        });

                    $_total_transactions_query = Transaction::query()
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
                        ->selectRaw('SUM(zero_rated_sales) as zeroRatedSales')
                        ->where('is_valid', true)
                        ->groupByRaw('DATE(created_at)')
                        ->orderByRaw('DATE(created_at)');

                    $_transactions_query = $_total_transactions_query
                        ->whereBetween('created_at', [$data['start_date'], $data['end_date']]);

                    $transactions = $_transactions_query->get();

                    $grandAccumulated = 0;

                    $_accumulated_balance = $_transactions_query
                        ->get()
                        ->groupBy( fn($transaction) => Carbon::parse($transaction->date)->format('Y-m-d') )
                        ->map(function ($transaction) use (&$grandAccumulated) {
                            $grandAccumulatedBeginning = $grandAccumulated;
                            $grandAccumulated += $transaction->first()->totalSales;
                            $grandAccumulatedEnding = $grandAccumulated;

                            return [
                                'grandBeginningBal' => $grandAccumulatedBeginning,
                                'grandEndingBal' => $grandAccumulatedEnding,
                            ];
                        });


                    $pdf = SnappyPdf::loadView('reports.bir-summary', [
                        'transactions' => $transactions,
                        'dailyRelationalData' => $dailyRelationalData,
                        'accumulatedBalance' => $_accumulated_balance,
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('BIR Summary Report.pdf');

                    // return response()->streamDownload(
                    //     fn () => print($pdf->output()),
                    //     'document.pdf'
                    // );
                }),

            Action::make('downloadGeneral')
                ->label('General Transaction Summary Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->default(now()),
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
                            ->required()
                            ->default(now()),
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
            ->where('is_valid', true)
            ->whereBetween('created_at', [Carbon::parse($data['start_date'])->startOfDay() , Carbon::parse($data['end_date'])->endOfDay()])
            ->when(auth()->user()->hasRole('Cashier'), function (Builder $query) {
                $query->where('processed_by', auth()->user()->id);
            })
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
