<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class TodayTab extends Widget
{
    protected static string $view = 'livewire.today-tab';

    public function getDisplayName(): string {
        return "Today";
    }
}
