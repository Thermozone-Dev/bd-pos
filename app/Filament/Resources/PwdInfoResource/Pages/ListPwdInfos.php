<?php

namespace App\Filament\Resources\PwdInfoResource\Pages;

use App\Filament\Resources\PwdInfoResource;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Actions\Action;
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
                ->action(function () {

                    $pdf = SnappyPdf::loadView('reports.pwd-summary')
                        ->setPaper('folio', 'landscape');

                    return $pdf->stream('E-3 - Persons with Disabilities Report.pdf');
                }),
        ];
    }
}
