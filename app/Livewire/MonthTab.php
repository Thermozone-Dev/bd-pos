<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class MonthTab extends Widget
{
    protected static string $view = 'livewire.month-tab';

    public function getColumns(){
        return 4;
    }

    public function getDisplayName(): string {
        return "Month";
    }
}
