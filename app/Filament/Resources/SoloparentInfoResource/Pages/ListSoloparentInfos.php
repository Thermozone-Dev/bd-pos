<?php

namespace App\Filament\Resources\SoloparentInfoResource\Pages;

use App\Filament\Resources\SoloparentInfoResource;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSoloparentInfos extends ListRecords
{
    protected static string $resource = SoloparentInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('Download BIR E-5 Report PDF')
                ->action(function () {

                    $pdf = SnappyPdf::loadView('reports.sp-summary')
                        ->setPaper('folio', 'landscape');

                    return $pdf->stream('E-5 - Solo Parent Report.pdf');
                }),
        ];
    }
}
