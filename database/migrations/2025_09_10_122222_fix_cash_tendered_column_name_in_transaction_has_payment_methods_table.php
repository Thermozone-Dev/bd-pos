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
        Schema::table('transaction_has_payment_methods', function (Blueprint $table) {
            $table->renameColumn('cash-tendered', 'cash_tendered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_has_payment_methods', function (Blueprint $table) {
            $table->renameColumn('cash_tendered', 'cash-tendered');
        });
    }
};
