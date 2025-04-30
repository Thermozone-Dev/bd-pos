<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class WeeklySales extends ChartWidget
{
    protected static ?string $heading = 'Weekly Sales';

    protected function getData(): array
    {
        return [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thurs', 'Fri', 'Sat'],
            'datasets' => [
                [
                    'label' => 'Weekly Sales',
                    'data' => [123, 150, 80, 91, 120, 60],
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
