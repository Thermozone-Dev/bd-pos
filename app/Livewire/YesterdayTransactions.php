<?php

namespace App\Livewire;

use App\Models\Transaction;
use Filament\Widgets\Widget;

class YesterdayTransactions extends Widget
{
    public $total_transactions;

    protected static string $view = 'livewire.yesterday-transactions';
}
