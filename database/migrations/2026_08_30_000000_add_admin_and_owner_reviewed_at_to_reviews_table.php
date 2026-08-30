<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-role "seen" timestamps for the sidebar unread dots. Each role clears
     * its own column when it opens its Reviews page, so an admin viewing the
     * list never clears the owner's dot (and vice versa).
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->timestamp('admin_reviewed_at')->nullable()->after('is_visible');
            $table->timestamp('owner_reviewed_at')->nullable()->after('admin_reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['admin_reviewed_at', 'owner_reviewed_at']);
        });
    }
};
