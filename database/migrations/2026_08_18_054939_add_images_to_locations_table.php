<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guarded: an identical migration exists at 2026_08_14_090000, so on
        // some databases this column is already present. Without the guard
        // `migrate` aborts here and blocks every later migration.
        if (Schema::hasColumn('locations', 'images')) {
            return;
        }

        Schema::table('locations', function (Blueprint $table) {
            $table->json('images')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        // No-op: 2026_08_14_090000 owns this column's lifecycle.
    }
};