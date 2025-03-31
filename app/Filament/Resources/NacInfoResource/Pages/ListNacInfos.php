<?php

namespace App\Filament\Resources\NacInfoResource\Pages;

use App\Filament\Resources\NacInfoResource;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListNacInfos extends ListRecords
{
    protected static string $resource = NacInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('Download BIR E-4 Report PDF')
                ->action(function () {

                    $pdf = SnappyPdf::loadView('reports.nac-summary')
                        ->setPaper('folio', 'landscape');

                    return $pdf->stream('E-4 - National Athletes and Coaches Report.pdf');
                }),
        ];
    }
}
