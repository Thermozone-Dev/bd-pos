<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('z_records', function (Blueprint $table) {
            $table->string('report_date')->after('id')->nullable();
            $table->string('report_time')->after('report_date')->nullable();
            $table->string('start_time')->after('id')->nullable();
            $table->string('end_time')->after('start_time')->nullable();
            $table->string('beginning_si')->after('end_time')->nullable();
            $table->string('ending_si')->after('beginning_si')->nullable();
            $table->string('beginning_void')->after('ending_si')->nullable();
            $table->string('ending_void')->after('beginning_void')->nullable();
            $table->float('present_accumulated_sales')->after('reset_counter')->nullable();
            $table->float('previous_accumulated_sales')->after('present_accumulated_sales')->nullable();
            $table->float('sales_for_the_day')->after('previous_accumulated_sales')->nullable();
            $table->float('vatable_sales')->after('sales_for_the_day')->nullable();
            $table->float('vat')->after('vatable_sales')->nullable();
            $table->float('vat_exempt_sales')->after('vat')->nullable();
            $table->float('zero_rated_sales')->after('vat_exempt_sales')->nullable();
            $table->float('gross_amount')->after('zero_rated_sales')->nullable();
            $table->float('less_discount')->after('gross_amount')->nullable();
            $table->float('less_void')->after('less_discount')->nullable();
            $table->float('less_vat_adjust')->after('less_void')->nullable();
            $table->float('net_amount')->after('less_vat_adjust')->nullable();
            $table->float('sc_discounts')->after('net_amount')->nullable();
            $table->float('pwd_discounts')->after('sc_discounts')->nullable();
            $table->float('naac_discounts')->after('pwd_discounts')->nullable();
            $table->float('sp_discounts')->after('naac_discounts')->nullable();
            $table->float('other_discounts')->after('sp_discounts')->nullable();
            $table->float('void')->after('other_discounts')->nullable();
            $table->float('returns')->after('void')->nullable();
            $table->float('sc_adjustments')->after('returns')->nullable();
            $table->float('pwd_adjustments')->after('sc_adjustments')->nullable();
            $table->float('reg_discount_adjustments')->after('pwd_adjustments')->nullable();
            $table->float('zero_rated_adjustments')->after('reg_discount_adjustments')->nullable();
            $table->float('vat_on_return')->after('zero_rated_adjustments')->nullable();
            $table->float('other_vat_adjustments')->after('vat_on_return')->nullable();
            $table->float('cash_in_drawer')->after('other_vat_adjustments')->nullable();
            $table->float('gcash_payments')->after('cash_in_drawer')->nullable();
            $table->float('maya_payments')->after('gcash_payments')->nullable();
            $table->float('debit_payments')->after('maya_payments')->nullable();
            $table->float('credit_payments')->after('debit_payments')->nullable();
            $table->float('opening_fund')->after('credit_payments')->nullable();
            $table->float('withdrawal')->after('opening_fund')->nullable();
            $table->float('less_withdrawal')->after('withdrawal')->nullable();
            $table->float('payments_received')->after('less_withdrawal')->nullable();
            $table->float('short_over')->after('payments_received')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('z_records', function (Blueprint $table) {
            $table->dropColumn([
                'start_time',
                'end_time',
                'beginning_si',
                'ending_si',
                'beginning_void',
                'ending_void',
                'present_accumulated_sales',
                'previous_accumulated_sales',
                'sales_for_the_day',
                'vatable_sales',
                'vat',
                'vat_exempt_sales',
                'zero_rated_sales',
                'gross_amount',
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
                'report_date',
                'report_time'
            ]);
        });
    }
};
