<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class YesterdayTab extends Widget
{
    protected static string $view = 'livewire.yesterday-tab';

    public function getColumns(){
        return 4;
    }

    public function getDisplayName(): string {
        return "Yesterday";
    }

}
