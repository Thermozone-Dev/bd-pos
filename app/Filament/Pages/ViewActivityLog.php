<?php

namespace App\Filament\Pages;

use Noxo\FilamentActivityLog\Pages\ListActivities;

class ViewActivityLog extends ListActivities
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 11;
}
