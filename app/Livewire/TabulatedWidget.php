<?php

namespace App\Livewire;

use Kenepa\MultiWidget\MultiWidget;

class TabulatedWidget extends MultiWidget
{
    public array $widgets = [
        YesterdayTab::class,
        TodayTab::class,
        WeekTab::class,
        MonthTab::class,
        YearTab::class,
    ];

    public function shouldPersistMultiWidgetTabsInSession(): bool
    {
        return true;
    }

}
