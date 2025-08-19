<?php

namespace App\Filament\Pages;

use App\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
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
        $filters = $this->getFilters();
        // Export To PDF using Snappy
        $activities = Activity::query()
            ->when(!$this->isFiltersBlank(), function ($query) use ($filters) {
                foreach ($filters as $column => $filter)
                {
                    if (isEmpty($filter)) {
                        continue;
                    }

                    if ($column === 'date_range') {
                        $dates = explode(' - ', $filter);
                        $startDate = Carbon::parse($dates[0])->startOfDay();
                        $endDate = Carbon::parse($dates[1])->endOfDay();
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    } elseif ($column === 'causer') {
                        $query->whereHas('causer', function ($q) use ($filter) {
                            $q->where('name', 'like', "%{$filter}%");
                        });
                    } elseif ($column === 'subject_type') {
                        $query->where('subject_type', $filter);
                    } elseif ($column === 'subject_id') {
                        $query->where('subject_id', $filter);
                    } elseif ($column === 'event') {
                        $query->where('event', $filter);
                    }
                }
            })
            ->orderByDesc('created_at')
            ->get();

        $exportData = [];
        foreach($activities as $activity){
            array_push($exportData, [
                'id' => $activity->id ?? 'N/A',
                'event' => $activity->event ?? 'N/A',
                'causer_id' => $activity->causer_id ?? 'N/A',
                'causer' => $activity->causer_id == null ? 'N/A' : User::find($activity->causer_id)->name,
                'subject_type' => class_basename($activity->subject_type) ?? 'N/A',
                'subject_id' => $activity->subject_id ?? 'N/A',
                'properties' => json_encode($activity->properties) ?? 'N/A',
                'created_at' => $activity->created_at ?? 'N/A',
            ]);
        };

        $pdf = SnappyPdf::loadView('reports.activity-log', [
            'activities' => $exportData,
        ]);

        return $pdf->stream('activity_log.pdf');
    }
}
