<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Item;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $product = Product::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'product_type_id' => $data['product_type_id'],
            'product_tax_category' => $data['product_tax_category'],
            'pax' => $data['pax'],
            'sku' => $data['sku'],
        ]);

        $item = Item::create([
            'product_id' => $product->id,
            'type' => 'product',
        ]);

        return $product;
    }

}
