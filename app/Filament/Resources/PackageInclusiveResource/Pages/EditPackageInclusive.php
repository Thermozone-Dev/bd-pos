<?php

namespace App\Filament\Resources\PackageInclusiveResource\Pages;

use App\Filament\Resources\PackageInclusiveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackageInclusive extends EditRecord
{
    protected static string $resource = PackageInclusiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
