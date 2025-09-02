<?php

namespace App\Filament\Resources\TransactionSummaryDataResource\Pages;

use App\Filament\Resources\TransactionSummaryDataResource;
use App\Models\TransactionSummaryData;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ListTransactionSummaryData extends ListRecords
{
    protected static string $resource = TransactionSummaryDataResource::class;

    protected static ?string $title = 'Product Summary';

    public function mount(): void
    {
        // $data =TransactionSummaryData::query()
        //     ->select('name', 'price', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(total) as total_income'))
        //     ->groupBy('name')
        //     ->orderBy('name');
        // dd($data);
    }

    public function getTableRecordKey(Model $record): string
    {
        return uniqid();
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
