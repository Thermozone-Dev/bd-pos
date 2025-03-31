<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('downloadPdf')
                ->label('BIR Summary Report')
                ->action(function () {

                    $pdf = SnappyPdf::loadView('reports.bir-summary')
                        ->setPaper('folio', 'landscape');

                    return $pdf->stream('BIR Summary Report.pdf');

                    // return response()->streamDownload(
                    //     fn () => print($pdf->output()),
                    //     'document.pdf'
                    // );
                }),
        ];
    }
}
