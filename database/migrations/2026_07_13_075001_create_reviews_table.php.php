<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();       // customer who wrote it
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();   // listing being reviewed
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete(); // optional link to the booking
            $table->unsignedTinyInteger('rating'); // 1–5
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true); // admin can hide inappropriate reviews
            $table->timestamps();

            // One review per customer per listing
            $table->unique(['user_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};