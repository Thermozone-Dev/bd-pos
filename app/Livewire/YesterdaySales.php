<?php

namespace App\Livewire;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Illuminate\Support\Facades\DB;
use App\Traits\TransactionSummary;
class YesterdaySales extends ChartWidget
{
    use TransactionSummary;


    protected static ?string $heading = 'Yesterday Sales';

    protected function getData(): array
    {

        $results = $this->getEveryfourhours('yesterday');

        return [
            'labels' => $results->pluck('label'),
            'datasets' => [
                [
                    'label' => 'Yesterday Sales',
                    'data' => $results->pluck('total_amount'),
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
