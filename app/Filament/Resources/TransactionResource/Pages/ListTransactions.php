<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Exports\BirSummaryExport;
use App\Exports\TransactionExport;
use App\Filament\Resources\TransactionResource;
use App\Journal\Journal;
use App\Models\Transaction;
use App\Models\z_record;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Exception;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\form;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadGeneral')
                    ->label('Transaction Report PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->default(now()),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->default(now()),
                    ])
                    ->action(function(array $data) {

                        $report = $this->export_value($data);

                        $pdf = SnappyPdf::loadView('reports.general-transaction-summary', [
                            'transactions' => $report['transactions'],
                            'discountSummary' => $report['discountSummary'],
                        ])->setPaper('folio', 'landscape');

                        return $pdf->stream('General Transaction Summary Report.pdf');

                    }),
            Action::make('export_record_to_excel')
                        ->label('Export Record to Excel')
                        ->icon('fas-file-export')
                        ->color('success')
                        ->form([
                            DatePicker::make('start_date')
                                ->label('Start Date')
                                ->required()
                                ->default(now()),
                            DatePicker::make('end_date')
                                ->label('End Date')
                                ->required()
                                ->default(now()),
                        ])
                        ->action(function(array $data) {
                            $report = $this->export_value($data);

                            $export = new TransactionExport(
                                $report, //variable
                                'general_transaction' // blade path of export table
                            );
                            return Excel::download($export, 'General Transaction Summary Report.xlsx');

                        })
            ->label('Transaction Report Excel')
            ->icon('heroicon-m-ellipsis-vertical')
            ->size(ActionSize::Small)
            ->color('primary')
            ->button(),

            Action::make('reset_si')
                ->label('Reset Sales Invoice')
                ->requiresConfirmation()
                ->color('danger')
                ->visible( fn () =>  auth()->user()->hasRole('super_admin'))
                ->action( function (){

                    $test = Artisan::call('app:reset-sales-invoice');

                    if($test == 0){
                        Notification::make()
                            ->title(Artisan::output())
                            ->icon('heroicon-o-check')
                            ->iconColor('success')
                            ->send();
                        return;
                    }

                    if($test == 1){
                        Notification::make()
                            ->title('Error resetting sales invoice: ' . Artisan::output())
                            ->icon('heroicon-o-exclamation-triangle')
                            ->iconColor('danger')
                            ->send();
                        return;
                    }


                })
                ->icon('heroicon-o-exclamation-triangle')
        ];
    }

    public function export_value($data): array
    {

        $transactions = Transaction::query()
            ->where('is_valid', true)
            ->whereBetween('created_at', [Carbon::parse($data['start_date'])->startOfDay() , Carbon::parse($data['end_date'])->endOfDay()])
            ->when(auth()->user()->hasRole('Cashier'), function (Builder $query) {
                $query->where('processed_by', auth()->user()->id);
            })
            ->get();

        $discountSummary = [];

        foreach ($transactions as $transaction) {
            // Initialize counters
            $discounts = [
                'sc' => 0,
                'pwd' => 0,
                'nac' => 0,
                'solo_parent' => 0,
            ];

            $basket = $transaction->basket()->first();
            if (!$basket) continue;

            $items = $basket->items()->get();

            foreach ($items as $item) {
                // Skip if no discount
                if (empty($item->discount_value) || $item->discount_value == 0.00) {
                    continue;
                }
                switch ($item->discounts()->first()->discount_id) {
                    case 1:
                        $discounts['sc'] += $item->discount_value;
                        break;
                    case 2:
                        $discounts['pwd'] += $item->discount_value;
                        break;
                    case 3:
                        $discounts['nac'] += $item->discount_value;
                        break;
                    case 4:
                        $discounts['solo_parent'] += $item->discount_value;
                        break;
                }
            }

            $discountSummary[$transaction->id] = $discounts;
        }

        return [
            'transactions' => $transactions,
            'discountSummary' => $discountSummary,
        ];
    }
}
