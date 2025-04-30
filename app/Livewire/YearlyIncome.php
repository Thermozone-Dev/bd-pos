<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class YearlyIncome extends ChartWidget
{
    protected static ?string $heading = 'Yearly Income';

    protected function getData(): array
    {
        return [
            'labels' => ['Legend I', 'Legend II', 'Legend III'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => [150, 45, 120],
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
