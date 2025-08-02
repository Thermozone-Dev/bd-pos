<?php

namespace App\Filament\Resources\PackageInclusiveResource\Pages;

use App\Filament\Resources\PackageInclusiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackageInclusives extends ListRecords
{
    protected static string $resource = PackageInclusiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
