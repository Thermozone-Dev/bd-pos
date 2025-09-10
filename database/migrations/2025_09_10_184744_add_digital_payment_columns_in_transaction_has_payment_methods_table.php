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
            $table->string('reference_number')->nullable()->after('cash_tendered');
            $table->float('transaction_fee')->default(0)->after('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_has_payment_methods', function (Blueprint $table) {
            $table->dropColumn('reference_number');
            $table->dropColumn('transaction_fee');
        });
    }
};
