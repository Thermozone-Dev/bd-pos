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
        Schema::table('transaction_basket_items', function (Blueprint $table) {
            $table->float('discount_value');
            $table->float('total_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_basket_items', function (Blueprint $table) {
            $table->dropColumn('discount_value');
            $table->dropColumn('total_value');
        });
    }
};
