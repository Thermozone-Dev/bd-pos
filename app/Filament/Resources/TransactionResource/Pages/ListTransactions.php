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
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

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
                                'voids' => 0,
                                'day_total' => 0,
                            ];

                            foreach ($transactionsOfDay as $transaction) {
                                if ($transaction->basket) {
                                    match (true) {
                                        $transaction->is_sc => $deductions['sc'] += $transaction->basket->items->sum('discount_value'),
                                        $transaction->is_pwd => $deductions['pwd'] += $transaction->basket->items->sum('discount_value'),
                                        $transaction->is_nac => $deductions['naac'] += $transaction->basket->items->sum('discount_value'),
                                        $transaction->is_soloparent => $deductions['solo_parent'] += $transaction->basket->items->sum('discount_value'),
                                        !$transaction->is_valid => $deductions['voids'] += $transaction->total_sales,
                                    };

                                    $deductions['day_total'] += $transaction->basket->items->sum('discount_value');
                                }
                            }
                            return $deductions;
                        });

                    dd($dailyRelationalData);

                    $_transactions_query = Transaction::query()
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
                        ->orderByRaw('DATE(created_at)');

                    $transactions = $_transactions_query->get();

                    $transaction_deductions = [
                        'sc' => $_transactions_query->selectRaw('SUM(CASE WHEN is_sc = 1 THEN discount_value ELSE 0 END) as scDiscount'),
                        'pwd' => $_transactions_query->selectRaw('SUM(CASE WHEN is_pwd = 1 THEN discount_value ELSE 0 END) as pwdDiscount'),
                        'naac' => $_transactions_query->selectRaw('SUM(CASE WHEN is_nac = 1 THEN discount_value ELSE 0 END) as nacDiscount'),
                        'solo_parent' => $_transactions_query->selectRaw('SUM(CASE WHEN is_soloparent = 1 THEN discount_value ELSE 0 END) as soloParentDiscount'),
                        'other_discounts' => $_transactions_query->selectRaw('SUM(CASE WHEN vat_exempt NOT IN (1, 2, 3, 4) THEN discount_value ELSE 0 END) as otherDiscounts'),
                        'voids' => $_transactions_query->selectRaw('SUM(CASE WHEN is_voided = 1 THEN total_sales ELSE 0 END) as voids'),
                    ];
                    $transaction_adjustments = [
                        'sc' => $_transactions_query->selectRaw('SUM(CASE WHEN discount_id = 1 THEN vat_adjustment ELSE 0 END) as scAdjustment'),
                        'pwd' => $_transactions_query->selectRaw('SUM(CASE WHEN discount_id = 2 THEN vat_adjustment ELSE 0 END) as pwdAdjustment'),
                        'other_discounts' => $_transactions_query->selectRaw('SUM(CASE WHEN discount_id NOT IN (1, 2) THEN vat_adjustment ELSE 0 END) as otherAdjustments'),
                        ''
                    ];


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
