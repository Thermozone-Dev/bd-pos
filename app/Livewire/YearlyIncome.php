<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class YearlyIncome extends ChartWidget
{
    protected static ?string $heading = 'Yearly Income';

    protected static ?string $maxHeight = '250px';

    protected static ?string $pollingInterval = null;

    public array $chartData;

    protected function getData(): array
    {
        $colors = [];
        foreach($this->chartData['labels'] as $label){
            array_push($colors, generate_hex_color());
        }
        return [
            'labels' =>  $this->chartData['labels'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $this->chartData['data'],
                    'backgroundColor' => $colors,
                    'borderColor' => 'black',
                    'fill' => true,
                ],
            ],
        ];
    }

    protected static ?array $options = [
        'scales' => [
            'x' => [
                'display' => false,
            ],
            'y' => [
                'display' => false,
            ],
        ],
    ];

    protected function getType(): string
    {
        return 'doughnut';
    }
}
