<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Exports\BirSummaryExport;
use App\Exports\TransactionExport;
use App\Filament\Resources\TransactionResource;
use App\Journal\Journal;
use App\Models\Transaction;
use App\Models\z_record;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Exception;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\form;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
             Action::make('downloadPdf')
                ->label('Generate BIR Summary Report')
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
                    Select::make('export_type')
                        ->options([
                            'pdf' => 'PDF',
                            'excel' => 'Excel',
                        ])
                        ->required()

                        //add export type select csv, pdf

                ])
                ->action(function (array $data) {

                    $z_records = z_record::query()
                        ->whereBetween('created_at', [Carbon::parse($data['start_date'])->startOfDay() , Carbon::parse($data['end_date'])->endOfDay()])
                        ->orderBy('report_date', 'asc')
                        ->orderBy('report_time', 'asc')
                        ->get();

                    $formatted_data = $z_records->map(function($record) {
                            return [
                                'Date' => Carbon::parse($record->report_date)->format('d/m/Y'),
                                'Beginning SI' => $record->beginning_si,
                                'Ending SI' => $record->ending_si,
                                'Accumulated End Bal' => $record->present_accumulated_sales,
                                'Accumulated Beg Bal' => $record->previous_accumulated_sales,
                                'Gross Sales' => $record->sales_for_the_day,
                                'Vatable Sales' => $record->vatable_sales,
                                'VAT' => $record->vat,
                                'VAT Exempt Sales' => $record->vat_exempt_sales,
                                'Zero Rated Sales' => $record->zero_rated_sales,
                                'SC Discounts' => $record->sc_discounts,
                                'PWD Discounts' => $record->pwd_discounts,
                                'NAAC Discounts' => $record->naac_discounts,
                                'Solo Parent Discounts' => $record->sp_discounts,
                                'Other Discounts' => $record->other_discounts,
                                'Void' => $record->void,
                                'Returns' => $record->returns,
                                'Total Deductions' => $record->total_discounts + $record->void,
                                'SC Adjustments' => $record->sc_adjustments,
                                'PWD Adjustments' => $record->pwd_adjustments,
                                'Reg Discount Adjustments' => $record->reg_discount_adjustments,
                                'VAT on Return' => $record->vat_on_return,
                                'Other VAT Adjustments' => $record->other_vat_adjustments,
                                'Total VAT Adjustments' => $record->total_vat_adjusts,
                                'VAT Payable' => $record->vat - $record->total_vat_adjusts,
                                'NET Sales' => $record->vatable_sales + $record->vat_exempt_sales - $record->total_discounts,
                                'Overflow' => $record->short_over,
                                'Total Income' => $record->vatable_sales + $record->vat_exempt_sales,
                                'Reset Counter' => $record->reset_counter,
                                'Z Counter' => $record->counter,
                            ];
                        })->toArray();

                    // add if else based on export type
                    if ($data['export_type'] === 'excel') {
                        $export = new BirSummaryExport(
                            [
                                'data' => $formatted_data
                            ], //variable
                        );
                        return Excel::download($export, 'BIR Summary Report.xlsx');
                    }
                    else {

                        $pdf = SnappyPdf::loadView('reports.bir-summary', [
                                'data' => $formatted_data
                        ])->setPaper('folio', 'landscape');

                        return $pdf->stream('BIR Summary Report.pdf');
                    }
                    // pdf



                    //excel
                    // export excel class in terminal


                    // return response()->streamDownload(
                    //     fn () => print($pdf->output()),
                    //     'document.pdf'
                    // );
                }),
            ActionGroup::make([
                Action::make('downloadJournal')
                ->label('E Journal')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    DatePicker::make('from')
                        ->label('From')
                        ->required()
                        ->default(now()),
                    DatePicker::make('to')
                        ->label('To')
                        ->required()
                        ->default(now()),
                    Toggle::make('include_reprint')
                        ->label('Include Reprints')
                        ->default(false)
                ])
                ->action(
                    function (array $data) {
                        if (Journal::journalDirectoryExists()){
                            Journal::clearJournalMergeFile();
                            $journals = Journal::getJournalEntries();

                            $journal_entries = $journals->filter(function ($item, $key) use ($data) {
                                $fileDate = Carbon::parse($item['created_at']);

                                $withinDateRange = $fileDate->between(Carbon::parse($data['from'])->startOfDay(), Carbon::parse($data['to'])->endOfDay());

                                if ($data['include_reprint']) {
                                    $reprintMatch = true; // Include all files if reprints are included
                                } else {
                                    $reprintMatch = !str_contains($item['path'], 'reprint');
                                }

                                return $withinDateRange &&  $reprintMatch;
                            })->sortBy(function ($file) {
                                return Carbon::parse($file['created_at'])->timestamp;
                            });

                            foreach ($journal_entries as $entry) {
                                $data = Journal::read($entry['path']);
                                $merge_file = Journal::getJournalPath();

                                Journal::append($merge_file, $data, true);
                            }

                            return response()->streamDownload(
                                fn () => readfile(storage_path('app/public/' . Journal::getJournalPath())),
                                'e-journal.txt'
                            );
                        }
                            Notification::make()
                                ->title('E-Journal file not found.')
                                ->warning()
                                ->send();
                    }
                ),

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
            ])
            ->label('More actions')
            ->icon('heroicon-m-ellipsis-vertical')
            ->size(ActionSize::Small)
            ->color('primary')
            ->button(),

            Action::make('reset_si')
                ->label('Reset Sales Invoice')
                ->requiresConfirmation()
                ->color('danger')
                ->visible( fn () =>  auth()->user()->hasRole('super_admin'))
                ->action( function (){

                    $test = Artisan::call('app:reset-sales-invoice');

                    if($test == 0){
                        Notification::make()
                            ->title(Artisan::output())
                            ->icon('heroicon-o-check')
                            ->iconColor('success')
                            ->send();
                        return;
                    }

                    if($test == 1){
                        Notification::make()
                            ->title('Error resetting sales invoice: ' . Artisan::output())
                            ->icon('heroicon-o-exclamation-triangle')
                            ->iconColor('danger')
                            ->send();
                        return;
                    }


                })
                ->icon('heroicon-o-exclamation-triangle')
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
