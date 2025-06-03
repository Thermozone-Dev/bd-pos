<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class TodayGrossTotal extends Widget
{
    public $total_sales;
    protected static string $view = 'livewire.today-gross-total';
}
