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
            $table->boolean('is_valid')->default(false)->change();
            $table->boolean('is_pwd')->default(false)->change();
            $table->boolean('is_sc')->default(false)->change();
            $table->boolean('is_nac')->default(false)->change();
            $table->boolean('is_soloparent')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_valid')->nullable();
            $table->boolean('is_pwd')->nullable();
            $table->boolean('is_sc')->nullable();
            $table->boolean('is_nac')->nullable();
            $table->boolean('is_soloparent')->nullable();
        });
    }
};
