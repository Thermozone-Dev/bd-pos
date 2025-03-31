<?php

namespace App\Filament\Resources\ScInfoResource\Pages;

use App\Filament\Resources\ScInfoResource;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Actions;
use Filament\Actions\Action;
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
                ->action(function () {

                    $pdf = SnappyPdf::loadView('reports.sc-summary')
                        ->setPaper('folio', 'landscape');

                    return $pdf->stream('E-2 - Senior Citizen Report.pdf');
                }),
        ];
    }
}
