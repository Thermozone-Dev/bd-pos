<?php

namespace App\Filament\Resources\ScInfoResource\Pages;

use App\Exports\TransactionExport;
use App\Filament\Resources\ScInfoResource;
use App\Models\ScInfo;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;

class ListScInfos extends ListRecords
{
    protected static string $resource = ScInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('Download BIR E-2 Report PDF')
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

                    $report = $this->export_value($data);


                    $pdf = SnappyPdf::loadView('reports.sc-summary', [
                        'scInfos' => $report['scInfos'],
                        'scTransactions' => $report['scTransactions'],
                        'transactionDiscounts' => $report['transactionDiscounts'],
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('E-2 - Senior Citizen Report.pdf');
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
                        'sc' // blade path of export table
                    );
                    return Excel::download($export, 'E-2 - Senior Citizen Report.xlsx');

                }),
        ];
    }


    public function export_value($data): array
    {
        $scInfos = ScInfo::query()
            ->whereBetween('created_at', [
                Carbon::parse($data['start_date'])->startOfDay(),
                Carbon::parse($data['end_date'])->endOfDay(),
            ])
            ->whereHas('transaction', function ($query) {
                $query->where('is_valid', true);
            })
            ->get();

        $transactionIds = $scInfos->pluck('transaction_id')->unique();
        $scTransactions = Transaction::whereIn('id', $transactionIds)
                ->when(auth()->user()->hasRole('Cashier'), function (Builder $query) {
                    $query->where('processed_by', auth()->user()->id);
                })
                ->get()->keyBy('id');

        $transactionDiscounts = [];

        foreach ($scTransactions as $transaction) {
            $totalDiscount = 0;

            foreach ($transaction->basket->items as $item) {
                $totalDiscount += $item->discount_value ?? 0;
            }

            $transactionDiscounts[$transaction->transaction_basket_id] = $totalDiscount;
        }

        return [
            'scInfos' => $scInfos,
            'scTransactions' => $scTransactions,
            'transactionDiscounts' => $transactionDiscounts,
        ];
    }

}
