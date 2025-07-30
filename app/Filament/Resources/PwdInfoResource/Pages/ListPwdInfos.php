<?php

namespace App\Filament\Resources\PwdInfoResource\Pages;

use App\Filament\Resources\PwdInfoResource;
use App\Models\PwdInfo;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

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

                    $pwdInfos = PwdInfo::query()
                        ->whereBetween('created_at', [Carbon::parse($data['start_date'])->startOfDay() , Carbon::parse($data['end_date'])->endOfDay()])
                        ->get();

                    $transactionIds = $pwdInfos->pluck('transaction_id')->unique();
                    $pwdTransactions = Transaction::whereIn('id', $transactionIds)->get()->keyBy('id');

                    $pdf = SnappyPdf::loadView('reports.pwd-summary', [
                        'pwdInfos' => $pwdInfos,
                        'pwdTransactions' => $pwdTransactions,
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('E-3 - Persons with Disabilities Report.pdf');
                }),
        ];
    }
}
