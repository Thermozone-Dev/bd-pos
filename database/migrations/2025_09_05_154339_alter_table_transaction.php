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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('si_no',12)->after('barcode')->nullable();
            $table->string('or_no',12)->after('si_no')->nullable();
            $table->string('reset_si_batch',12)->after('or_no')->nullable();
            $table->dateTime('last_reseted')->after('reset_si_batch')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'reset_si_batch',
                'or_no',
                'si_no',
                'last_reseted',
            ]);
        });
    }
};
