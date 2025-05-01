<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class YearlySales extends ChartWidget
{
    protected static ?string $heading = 'Yearly Sales';

    protected function getData(): array
    {
        return [
            'labels' => ['2020', '2021', '2022', '2023', '2024', '2025'],
            'datasets' => [
                [
                    'label' => 'Yearly Sales',
                    'data' => [5323, 5120, 4803, 7021, 8079, 8209],
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
