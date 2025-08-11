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
        Schema::table('soloparent_infos', function (Blueprint $table) {
            $table->string('child_name')->nullable()->change();
            $table->integer('child_age')->nullable()->change();
            $table->date('child_birthday')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soloparent_infos', function (Blueprint $table) {
            $table->string('child_name')->nullable(false)->change();
            $table->integer('child_age')->nullable(false)->change();
            $table->date('child_birthday')->nullable(false)->change();
        });
    }
};
