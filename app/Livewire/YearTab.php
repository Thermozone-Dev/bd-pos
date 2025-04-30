<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class YearTab extends Widget
{
    protected static string $view = 'livewire.year-tab';

    public function getDisplayName(): string {
        return "Year";
    }

    public function getColumns(){
        return 4;
    }

    protected int | string | array $columnSpan = 'full';
}
