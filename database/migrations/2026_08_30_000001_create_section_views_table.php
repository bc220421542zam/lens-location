<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-user "last visited this section" stamps. A sidebar dot means
     * "something new in this section since you last opened it" - opening the
     * page re-stamps the section, clearing the dot until the next new item.
     */
    public function up(): void
    {
        Schema::create('section_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('section', 50);
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_views');
    }
};
