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
        Schema::create('stubs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id'); // Foreign key to transactions table
            $table->unsignedBigInteger('package_inclusive_id'); // Foreign key to package inclusive table
            $table->integer('status')->comment('0: Pending, 1: Claimed, 2: Cancelled')->default(0);
            $table->string('stub_no')->default(now()->format('mdY-Hisv'));
            $table->dateTime('claimed_at')->unique()->default(now()->format('Y-m-d H:i:s')); // mdy
            $table->unsignedBigInteger('claimed_transact_by');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stubs');
    }
};
