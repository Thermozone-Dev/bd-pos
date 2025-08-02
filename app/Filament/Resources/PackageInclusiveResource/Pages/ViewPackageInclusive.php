<?php

namespace App\Filament\Resources\PackageInclusiveResource\Pages;

use App\Filament\Resources\PackageInclusiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPackageInclusive extends ViewRecord
{
    protected static string $resource = PackageInclusiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
