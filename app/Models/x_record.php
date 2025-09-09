<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class x_record extends Model
{
    protected $table = 'x_records';

    protected $fillable = [
        'generated_by',
        'report_date',
        'report_time',
        'start_time',
        'end_time',
        'cashier_name',
        'beginning_si',
        'ending_si',
        'opening_fund',
        'cash_payments',
        'gcash_payments',
        'maya_payments',
        'debit_payments',
        'credit_payments',
        'total_payments',
        'void',
        'withdrawal',
        'cash_in_drawer',
        'less_withdrawal',
        'short_over',
    ];
}
