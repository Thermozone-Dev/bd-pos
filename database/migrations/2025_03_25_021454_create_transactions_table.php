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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('processed_by');
            $table->unsignedBigInteger('transaction_basket_id');
            $table->string('barcode');
            $table->string('transaction_method');
            $table->float('transaction_fee');
            $table->float('cash_tendered');
            $table->float('change');
            $table->float('vatable_sales');
            $table->float('vat');
            $table->float('vat_exempt_sales');
            $table->float('vat_exempt');
            $table->float('zero_rated_sales');
            $table->boolean('is_valid');
            $table->boolean('is_pwd');
            $table->boolean('is_sc');
            $table->boolean('is_nac');
            $table->boolean('is_soloparent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
