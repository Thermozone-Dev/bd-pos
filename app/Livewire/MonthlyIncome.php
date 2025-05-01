<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class MonthlyIncome extends ChartWidget
{
    protected static ?string $heading = 'Monthly Income';

    protected function getData(): array
    {
        return [
            'labels' => ['Legend I', 'Legend II', 'Legend III'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => [1650, 350, 1100],
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
