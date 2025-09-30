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
            $table->double('total_discounts')->after('gross_amount')->nullable();
            $table->double('total_vat_adjusts')->after('total_discounts')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('z_records', function (Blueprint $table) {
            $table->dropColumn([
                'total_discounts',
                'total_vat_adjusts',
            ]);
        });
    }
};
