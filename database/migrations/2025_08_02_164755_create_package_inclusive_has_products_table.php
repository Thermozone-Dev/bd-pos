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
        Schema::create('package_inclusive_has_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_inclusives_id'); // Foreign key to package_inclusives table
            $table->unsignedBigInteger('product_id'); // Foreign key to package_inclusives table
            $table->integer('qty')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_inclusive_has_products');
    }
};
