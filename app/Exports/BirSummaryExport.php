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
        $data = $this->records['data'];

       return view('reports.bir-excel-report',compact('data') );
    }

}
