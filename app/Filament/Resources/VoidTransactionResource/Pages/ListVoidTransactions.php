<?php

namespace App\Filament\Resources\VoidTransactionResource\Pages;

use App\Exports\TransactionExport;
use App\Filament\Resources\VoidTransactionResource;
use App\Models\VoidTransaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListVoidTransactions extends ListRecords
{
    protected static string $resource = VoidTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('Export to PDF')
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

                    $pdf = SnappyPdf::loadView('reports.void-summary', [
                        'voidInfos' => $report['voidInfos'],
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('Void Transactions Report.pdf');
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
                        'void' // blade path of export table
                    );
                    return Excel::download($export, 'Void Transactions Report.xlsx');

                }),
        ];
    }

    public function export_value($data): array
    {
        $voidInfos = VoidTransaction::query()
            ->whereBetween('created_at', [
                Carbon::parse($data['start_date'])->startOfDay(),
                Carbon::parse($data['end_date'])->endOfDay(),
            ])->get();

        return [
            'voidInfos' => $voidInfos,
        ];
    }
}
