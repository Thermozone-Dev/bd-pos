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
        Schema::create('transaction_basket_has_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_basket_id');
            $table->unsignedBigInteger('discount_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_basket_has_discounts');
    }
};
