<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        //  add the new column 
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('photographer_id');
        });

        // copy data from photographer_id into customer_id
        DB::statement('UPDATE bookings SET customer_id = photographer_id');

        //  drop the old column
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('photographer_id');
        });


        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('photographer_id')->nullable()->after('location_id');
        });

        DB::statement('UPDATE bookings SET photographer_id = customer_id');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
    }
};