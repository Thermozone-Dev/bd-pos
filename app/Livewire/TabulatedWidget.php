<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\TransactionBasket;
use App\Models\TransactionBasketItem;
use Illuminate\Support\Facades\DB;
use Kenepa\MultiWidget\MultiWidget;
use App\Livewire\TodayTab;
use Filament\Infolists\Components\Livewire;
use PDO;

class TabulatedWidget extends MultiWidget
{

    public array $data = [];

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

