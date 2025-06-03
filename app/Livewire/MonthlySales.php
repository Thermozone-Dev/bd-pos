<?php

namespace App\Livewire;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class MonthlySales extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales';

    protected function getData(): array
    {
        $data = Trend::model(Transaction::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->sum('total_sales');


        return [
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->isoFormat('MMM') ),
            'datasets' => [
                [
                    'label' => 'Monthly Sales',
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
