<?php

namespace App\Filament\Resources\PackageResource\Pages;

use App\Filament\Resources\PackageResource;
use App\Models\Item;
use App\Models\Package;
use App\Models\PackageHasProduct;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePackage extends CreateRecord
{
    protected static string $resource = PackageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $package = Package::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'pax' => $data['pax'],
            'product_tax_category' => $data['package_tax_category'],
        ]);

        $item = Item::create([
            'package_id' => $package->id,
            'type' => 'package',
        ]);

        return $package;
    }

}
