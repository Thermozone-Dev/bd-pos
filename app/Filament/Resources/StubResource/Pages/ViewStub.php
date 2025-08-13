<?php

namespace App\Filament\Resources\StubResource\Pages;

use App\Filament\Resources\StubResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewStub extends ViewRecord
{
    protected static string $resource = StubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAsClaimed')
                ->label('Mark as Claimed')
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->record->update([
                        'status' => 1,
                    ]);
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 0),
        ];
    }
}
