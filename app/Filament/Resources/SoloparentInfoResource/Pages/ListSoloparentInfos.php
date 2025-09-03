<?php

namespace App\Filament\Resources\SoloparentInfoResource\Pages;

use App\Exports\TransactionExport;
use App\Filament\Resources\SoloparentInfoResource;
use App\Models\SoloparentInfo;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;

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
                        ->default(now()),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data) {
                    $report = $this->export_value($data);
                    $pdf = SnappyPdf::loadView('reports.sp-summary', [
                        'spInfos' => $report['spInfos'],
                        'spTransactions' => $report['spTransactions'] ,
                        'transactionDiscounts' => $report['transactionDiscounts'],
                    ])
                    ->setPaper('folio', 'landscape');

                    return $pdf->stream('E-5 - Solo Parent Report.pdf');
                }),

                Action::make('export_record_to_excel')
                ->label('Export Record to Excel')
                ->icon('fas-file-export')
                ->color('success')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->default(now())
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
                        'sp' // blade path of export table
                    );
                    return Excel::download($export, 'E-5 - Solo Parent Report.xlsx');

                }),
        ];
    }

    public function export_value($data): array
    {

        $spInfos = SoloparentInfo::query()
            ->whereBetween('created_at', [Carbon::parse($data['start_date'])->startOfDay() , Carbon::parse($data['end_date'])->endOfDay()])
            ->whereHas('transaction', function ($query) {
                $query->where('is_valid', true);
            })
            ->get();

        $transactionIds = $spInfos->pluck('transaction_id')->unique();

        $spTransactions = Transaction::whereIn('id', $transactionIds)
            ->with('basket.items')
            ->when(auth()->user()->hasRole('Cashier'), function (Builder $query) {
                $query->where('processed_by', auth()->user()->id);
            })
            ->get();

        $transactionDiscounts = [];

        foreach ($spTransactions as $transaction) {
            $totalDiscount = 0;

            foreach ($transaction->basket->items as $item) {
                $totalDiscount += $item->discount_value ?? 0;
            }

            $transactionDiscounts[$transaction->transaction_basket_id] = $totalDiscount + $transaction->vat_adjustment;
        }
        return [
            'spInfos' => $spInfos,
            'spTransactions' => $spTransactions,
            'transactionDiscounts' => $transactionDiscounts,
        ];

    }
}
