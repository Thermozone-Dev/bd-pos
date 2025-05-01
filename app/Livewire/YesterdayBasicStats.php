<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class YesterdayBasicStats extends Widget
{
    protected static string $view = 'livewire.yesterday-basic-stats';

    protected int | string | array $columnSpan = 'full';
}
