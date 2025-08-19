<?php

namespace App\Filament\Pages;

use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Actions\Action;
use Noxo\FilamentActivityLog\Pages\ListActivities;
use Spatie\Activitylog\Models\Activity;

use function PHPUnit\Framework\isEmpty;

class ViewActivityLog extends ListActivities
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 11;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export-logs')
                ->label('Export Logs')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->action(function () {
                    $this->exportLogs();
                }),
        ];
    }

    public function isFiltersBlank(): bool
    {
        $values = request()->only(
            array_keys($this->getFilters()),
        );

        return count($values) === 0;
    }

    public function exportLogs()
    {
        // Export To PDF using Snappy
        $activities = Activity::query()
            ->when(! $this->isFiltersBlank(), function ($query) {
                $query->filter($this->getFilters());
            })
            ->orderByDesc('created_at');

        $exportData = [];
        foreach($activities as $activity){
            array_push($exportData, [
                'id' => $activity->id,
                'event' => $activity->event,
                'causer_id' => $activity->causer_id,
                'causer' => User::find($activity->causer_id)->name,
                'subject_type' => class_basename($activity->subject_type),
                'subject_id' => $activity->subject_id,
                'properties' => json_encode($activity->properties),
                'created_at' => $activity->created_at,
            ]);
        };

        $pdf = SnappyPdf::loadView('activity-log.export', [
            'activities' => $exportData,
        ]);

        return $pdf->stream('activity_log.pdf');
    }
}
