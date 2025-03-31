<?php

namespace App\Filament\Resources\SoloparentInfoResource\Pages;

use App\Filament\Resources\SoloparentInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSoloparentInfos extends ListRecords
{
    protected static string $resource = SoloparentInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
