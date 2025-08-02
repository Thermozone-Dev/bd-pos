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
        Schema::create('package_inclusives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stall_id')->nullable(); //tereken
            $table->string('name',100)->nullable(); //
            $table->float('price', 8, 2)->default(0.00);
            $table->text('description',300)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_inclusives');
    }
};
