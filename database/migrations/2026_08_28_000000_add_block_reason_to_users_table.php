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
        // No after() clauses: this database has drifted from its own
        // migrations, so positional anchors aren't reliable here.
        Schema::table('users', function (Blueprint $table) {
            $table->string('block_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['block_reason', 'blocked_at']);
        });
    }
};
