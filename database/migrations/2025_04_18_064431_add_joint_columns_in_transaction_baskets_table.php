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
        Schema::table('transaction_baskets', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('transaction_basket_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_baskets', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
            $table->dropColumn('transaction_basket_item_id');
        });
    }
};
