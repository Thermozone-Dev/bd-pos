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
            $table->float('accumulated_sales')
                ->default(0)
                ->after('reset_counter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('z_records', function (Blueprint $table) {
            $table->dropColumn('accumulated_sales');
        });
    }
};
