<?php

namespace App\Filament\Pages;

use App\Livewire\Income;
use App\Livewire\YesterdayBasicStats;
use App\Livewire\YesterdayGrossTotal;
use App\Livewire\YesterdaySales;
use App\Livewire\YesterdayTransactions;
use Filament\Widgets\AccountWidget;
use Symfony\Component\HttpKernel\Profiler\Profile;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected function getHeaderWidgets(): array
    {
        return [
            // AccountWidget:ss:class,
            // YesterdayBasicStats::class,
            // YesterdaySales::class,
            // Income::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | string | array
    {
        return 3;
    }
}
