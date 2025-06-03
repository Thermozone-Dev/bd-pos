<?php

namespace App\Livewire;

use Filament\Widgets\Widget;

class YearlyTransactions extends Widget
{

    public $total_transactions;

    protected static string $view = 'livewire.yearly-transactions';
}
