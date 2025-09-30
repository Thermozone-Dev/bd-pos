<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class z_record extends Model
{
    protected $fillable =[
        'report_date',
        'report_time',
        'start_time',
        'end_time',
        'beginning_si',
        'ending_si',
        'beginning_void',
        'ending_void',
        'counter',
        'reset_counter',
        'present_accumulated_sales',
        'previous_accumulated_sales',
        'sales_for_the_day',
        'vatable_sales',
        'vat',
        'vat_exempt_sales',
        'zero_rated_sales',
        'gross_amount',
        'total_discounts',
        'total_vat_adjusts',
        'less_discount',
        'less_void',
        'less_vat_adjust',
        'net_amount',
        'sc_discounts',
        'pwd_discounts',
        'naac_discounts',
        'sp_discounts',
        'other_discounts',
        'void',
        'returns',
        'sc_adjustments',
        'pwd_adjustments',
        'reg_discount_adjustments',
        'zero_rated_adjustments',
        'vat_on_return',
        'other_vat_adjustments',
        'cash_in_drawer',
        'gcash_payments',
        'maya_payments',
        'debit_payments',
        'credit_payments',
        'opening_fund',
        'withdrawal',
        'less_withdrawal',
        'payments_received',
        'short_over',
    ];
}
