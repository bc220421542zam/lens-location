<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->constrained('payouts')->cascadeOnDelete();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->unique(['payout_id', 'transaction_id'], 'payout_transaction_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_transaction');
    }
};
