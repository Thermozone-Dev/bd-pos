<?php

namespace App\Livewire;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class YearlySales extends ChartWidget
{
    protected static ?string $heading = 'Yearly Sales';

    protected function getData(): array
    {

        $data = Trend::model(Transaction::class)
            ->between(
                start: now()->subYears(5),
                end: now()->endOfYear(),
            )
            ->perYear()
            ->sum('total_sales');

        return [
            'labels' => $data->map(fn (TrendValue $value) => $value->date ),
            'datasets' => [
                [
                    'label' => 'Yearly Sales',
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
