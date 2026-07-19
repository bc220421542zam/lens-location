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
        Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
    $table->foreignId('customer_id')->constrained('users');
    $table->foreignId('owner_id')->constrained('users');
    $table->decimal('amount', 10, 2);
    $table->decimal('owner_earning', 10, 2);
    $table->string('jazzcash_txn_ref')->nullable();
    $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
    $table->enum('payout_status', ['unpaid', 'paid'])->default('unpaid');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
