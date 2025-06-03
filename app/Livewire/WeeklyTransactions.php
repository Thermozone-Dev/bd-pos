<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class WeeklyTransactions extends Widget
{
    public $total_transactions;

    protected static string $view = 'livewire.weekly-transactions';
}
