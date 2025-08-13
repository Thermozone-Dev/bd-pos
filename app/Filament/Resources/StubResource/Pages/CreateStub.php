<?php

namespace App\Filament\Resources\StubResource\Pages;

use App\Filament\Resources\StubResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStub extends CreateRecord
{
    protected static string $resource = StubResource::class;
}
