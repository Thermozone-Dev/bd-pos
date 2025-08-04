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
        Schema::table('stubs', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->after('package_inclusive_id')->comment('Quantity of the package inclusive in the stub');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stubs', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
