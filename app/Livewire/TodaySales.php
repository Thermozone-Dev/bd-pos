<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class TodaySales extends ChartWidget
{
    protected static ?string $heading = 'Today\'s Sales';

    protected function getData(): array
    {
        return [
            'labels' => ['4 AM', '8 AM', '12 PM', '4 PM', '8 PM', '12 AM'],
            'datasets' => [
                [
                    'label' => 'Today\'s Sales',
                    'data' => [32, 20, 50, 81, 20, 5],
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
