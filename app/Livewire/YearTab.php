<?php

namespace App\Livewire;

use App\Traits\TransactionSummary;
use Filament\Widgets\Widget;
use Illuminate\Support\Arr;

class YearTab extends Widget
{

    use TransactionSummary;

    protected static string $view = 'livewire.year-tab';

    public $data;

    public function mount(): void
    {

        $data = $this->transactionSummary('year');
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


    public function getDisplayName(): string {
        return "Year";
    }

    public function getColumns(){
        return 4;
    }

    protected int | string | array $columnSpan = 'full';
}
