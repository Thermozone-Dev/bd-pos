<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class MonthlySales extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales';

    protected function getData(): array
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => [
                [
                    'label' => 'Monthly Sales',
                    'data' => [223, 520, 803, 1021, 879, 1209, 904, 393, 604, 845, 589, 749],
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
