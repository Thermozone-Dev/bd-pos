<?php

namespace App\Livewire;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WeeklySales extends ChartWidget
{
    protected static ?string $heading = 'Weekly Sales';

    protected function getData(): array
    {

        $data = Trend::model(Transaction::class)
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->sum('total_sales');


        return [
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->isoFormat('ddd') ),
            'datasets' => [
                [
                    'label' => 'Weekly Sales',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'red',
                    'borderColor' => 'red',
                    'fill' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
