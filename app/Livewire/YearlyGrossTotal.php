<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class YearlyGrossTotal extends Widget
{
    public $total_sales;
    protected static string $view = 'livewire.yearly-gross-total';
}
