<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class WeekTab extends Widget
{
    protected static string $view = 'livewire.week-tab';

    public function getColumns(){
        return 4;
    }

    public function getDisplayName(): string {
        return "Week";
    }
}
