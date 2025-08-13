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
            $table->dropUnique('stubs_stub_no_unique');
            $table->string('stub_no', 255)->default(null)->change();

            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stubs', function (Blueprint $table) {
            $table->string('stub_no', 255)->unique()->change();
            //
        });
    }
};
