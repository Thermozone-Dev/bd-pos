<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class TodayIncome extends ChartWidget
{
    protected static ?string $heading = 'Today\'s Income';

    protected function getData(): array
    {
        return [
            'labels' => ['Legend I', 'Legend II', 'Legend III'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => [90, 45, 50],
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
