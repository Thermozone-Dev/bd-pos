<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class WeeklyGrossTotal extends Widget
{
    public $total_sales;

    protected static string $view = 'livewire.weekly-gross-total';
}
