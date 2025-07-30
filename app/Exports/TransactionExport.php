<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class TransactionExport implements FromView
{

    protected $records;



    public function __construct(array $records, $type)
    {
        $this->records = $records;
        $this->records['exporttype'] = $type;
    }

    public function view(): View
    {
        return view('reports.export', $this->records);
    }
}
