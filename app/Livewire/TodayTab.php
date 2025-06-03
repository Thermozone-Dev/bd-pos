<?php

namespace App\Livewire;

use App\Traits\TransactionSummary;
use Filament\Widgets\Widget;
use Illuminate\Support\Arr;

class TodayTab extends Widget
{
    use TransactionSummary;

    protected static string $view = 'livewire.today-tab';

    public $data;

    public function mount(): void
    {

        $data = $this->transactionSummary('today');
        $this->data['income'] = [
            'labels' =>  Arr::pluck($data['per_categories'], 'name'),
            'data' =>  Arr::pluck($data['per_categories'], 'income'),
            'total' =>  $data['total_per_categoies'],
        ];
        $this->data['total_transaction'] =  $data['total_transaction'];
        $this->data['total_sales'] =  $data['total_sales'];
        $this->data['product_trend'] =  $data['product_trends'];
        $this->data['best_employee'] =  $data['best_employee'];
    }


    public function getColumns(){
        return 4;
    }

    public function getDisplayName(): string {
        return "Today";
    }
}
