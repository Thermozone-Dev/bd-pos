<?php

namespace App\Filament\Resources\PwdInfoResource\Pages;

use App\Exports\TransactionExport;
use App\Filament\Resources\PwdInfoResource;
use App\Models\PwdInfo;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;

class ListPwdInfos extends ListRecords
{
    protected static string $resource = PwdInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('Download BIR E-3 Report PDF')
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

                    $report = $this->export_value($data);

                    $pdf = SnappyPdf::loadView('reports.pwd-summary', [
                        'pwdInfos' => $report['pwdInfos'],
                        'pwdTransactions' => $report['pwdTransactions'],
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('E-3 - Persons with Disabilities Report.pdf');
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
                            'pwd' // blade path of export table
                        );
                        return Excel::download($export, 'E-3 - Persons with Disabilities Report.xlsx');
                    }),

        ];
    }

    public function export_value($data): array
    {

        $pwdInfos = PwdInfo::query()
            ->whereBetween('created_at', [Carbon::parse($data['start_date'])->startOfDay() , Carbon::parse($data['end_date'])->endOfDay()])
            ->whereHas('transaction', function ($query) {
                $query->where('is_valid', true);
            })
            ->get();

        $transactionIds = $pwdInfos->pluck('transaction_id')->unique();
        $pwdTransactions = Transaction::whereIn('id', $transactionIds)
                ->when(auth()->user()->hasRole('Cashier'), function (Builder $query) {
                    $query->where('processed_by', auth()->user()->id);
                })
                ->get()->keyBy('id');

        return [
            'pwdInfos' => $pwdInfos,
            'pwdTransactions' => $pwdTransactions,
        ];
    }

}
