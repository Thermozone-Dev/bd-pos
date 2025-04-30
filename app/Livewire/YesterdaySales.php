<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class YesterdaySales extends ChartWidget
{
    protected static ?string $heading = 'Yesterday Sales';

    protected function getData(): array
    {
        return [
            'labels' => ['4 AM', '8 AM', '12 PM', '4 PM', '8 PM', '12 AM'],
            'datasets' => [
                [
                    'label' => 'Yesterday Sales',
                    'data' => [65, 59, 80, 81, 56, 55],
                    'backgroundColor' => 'red',
                    'borderColor' => 'red',
                    'fill' => false,
                ],
            ],
        ];
    }

    protected int | string | array $columnSpan = 2;

    protected function getType(): string
    {
        return 'line';
    }
}
