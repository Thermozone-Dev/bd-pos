<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class BirSummaryExport implements FromView
{
    protected $records;


    public function __construct(array $export)
    {
        $this->records = $export;
    }

    public function view(): View
    {
        $transactions = $this->records['transactions'];
        $dailyRelationalData = $this->records['dailyRelationalData'];
        $accumulatedBalance = $this->records['accumulatedBalance'];

       return view('reports.bir-excel-report',compact('transactions','dailyRelationalData','accumulatedBalance') );
    }

}
