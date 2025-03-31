<?php

namespace App\Filament\Resources\NacInfoResource\Pages;

use App\Filament\Resources\NacInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNacInfos extends ListRecords
{
    protected static string $resource = NacInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
