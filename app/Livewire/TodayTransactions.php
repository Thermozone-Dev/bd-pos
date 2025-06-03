<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class TodayTransactions extends Widget
{
    public $total_transactions;

    protected static string $view = 'livewire.today-transactions';
}
