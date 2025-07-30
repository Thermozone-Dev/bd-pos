<?php

namespace App\Filament\Resources\ScInfoResource\Pages;

use App\Filament\Resources\ScInfoResource;
use App\Models\ScInfo;
use App\Models\Transaction;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

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
                        ->default(now()->endOfDay()),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->default(now()->endOfWeek()),
                ])
                ->action(function (array $data) {

                    $scInfos = ScInfo::query()
                        ->whereBetween('created_at', [Carbon::parse($data['start_date'])->startOfDay() , Carbon::parse($data['end_date'])->endOfDay()])
                        ->get();

                    $transactionIds = $scInfos->pluck('transaction_id')->unique();
                    $scTransactions = Transaction::whereIn('id', $transactionIds)->get()->keyBy('id');

                    $pdf = SnappyPdf::loadView('reports.sc-summary', [
                        'scInfos' => $scInfos,
                        'scTransactions' => $scTransactions,
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('E-2 - Senior Citizen Report.pdf');
                }),
        ];
    }
}
