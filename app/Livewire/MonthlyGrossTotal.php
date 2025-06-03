<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class MonthlyGrossTotal extends Widget
{
    public $total_sales;


    protected static string $view = 'livewire.monthly-gross-total';
}
