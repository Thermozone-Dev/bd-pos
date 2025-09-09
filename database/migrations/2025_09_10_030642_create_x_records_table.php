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
        Schema::create('x_records', function (Blueprint $table) {
            $table->id();
            $table->string('report_date');
            $table->string('report_time');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('cashier_name');
            $table->string('beginning_si');
            $table->string('ending_si');
            $table->float('opening_fund');
            $table->float('cash_payments');
            $table->float('gcash_payments');
            $table->float('maya_payments');
            $table->float('debit_payments');
            $table->float('credit_payments');
            $table->float('total_payments');
            $table->float('void');
            $table->float('withdrawal');
            $table->float('cash_in_drawer');
            $table->float('less_withdrawal');
            $table->float('short_over');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x_records');
    }
};
