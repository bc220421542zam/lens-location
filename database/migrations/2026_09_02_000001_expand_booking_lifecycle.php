<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Booking-to-payout lifecycle completion:
     *  - bookings gain an `expired` state (confirmed but never paid in time),
     *  - transactions record when a payout transfer actually landed
     *    (`transferred_at`) and when a dispute was raised (`disputed_at`).
     */
    public function up(): void
    {
        // Appending a value to the end of the list keeps existing rows valid
        // under MySQL strict mode.
        $this->setBookingStatusEnum(['pending', 'confirmed', 'completed', 'cancelled', 'expired']);

        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('transferred_at')->nullable();
            $table->timestamp('disputed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transferred_at', 'disputed_at']);
        });

        // Rows already flipped to expired would violate the narrower enum, so
        // migrate them back to the state they left (confirmed, never paid).
        DB::table('bookings')->where('status', 'expired')->update(['status' => 'confirmed']);

        $this->setBookingStatusEnum(['pending', 'confirmed', 'completed', 'cancelled']);
    }

    /**
     * Driver-branch enum change copied from the 2026_08_24_100100 migration:
     * MySQL keeps a native ENUM, SQLite converts to a plain string column.
     */
    private function setBookingStatusEnum(array $values): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $quoted = collect($values)->map(fn ($v) => "'{$v}'")->join(', ');
            DB::statement("ALTER TABLE `bookings` MODIFY `status` ENUM({$quoted}) NOT NULL DEFAULT 'pending'");

            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
