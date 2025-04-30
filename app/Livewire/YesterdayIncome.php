<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class YesterdayIncome extends ChartWidget
{
    protected static ?string $heading = 'Yesterday\'s Income';

    protected function getData(): array
    {
        return [
            'labels' => ['Legend I', 'Legend II', 'Legend III'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => [78, 30, 88],
                    'backgroundColor' => ['#850101', 'black', '#d4d404'],
                    'borderColor' => 'black',
                    'fill' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
