<?php

namespace App\Filament\Resources\ScInfoResource\Pages;

use App\Filament\Resources\ScInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScInfos extends ListRecords
{
    protected static string $resource = ScInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
