<?php

namespace App\Filament\Resources\StubResource\Pages;

use App\Filament\Resources\StubResource;
use App\Models\Stub;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListStubs extends ListRecords
{
    protected static string $resource = StubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_record_to_pdf')
                ->label('Export Record to PDF')
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

                    $pdf = SnappyPdf::loadView('reports.stubs-summary', [
                        'stubs' => $report['stubs'],
                    ])->setPaper('folio', 'landscape');

                    return $pdf->stream('E-2 - Senior Citizen Report.pdf');

                }),
        ];
    }

    public function export_value($data): array
    {
        $stubs = Stub::query()
            ->whereBetween('created_at', [
                Carbon::parse($data['start_date'])->startOfDay(),
                Carbon::parse($data['end_date'])->endOfDay(),
            ])
            ->get();

        return [
            'stubs' => $stubs,
        ];
    }
}
