<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class MonthlyTransactions extends Widget
{
    public $total_transactions;
    protected static string $view = 'livewire.monthly-transactions';
}
