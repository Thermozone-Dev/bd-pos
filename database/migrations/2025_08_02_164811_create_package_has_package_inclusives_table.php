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
        Schema::create('package_has_package_inclusives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->nullable(); // Foreign key to packages table
            $table->unsignedBigInteger('package_inclusive_id')->nullable(); // Foreign key to package_inclusives table
            $table->float('price', 8, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_has_package_inclusives');
    }
};
