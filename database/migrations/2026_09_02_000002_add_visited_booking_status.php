<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * New terminal state for the paid path of the booking lifecycle:
     *
     *  pending -> confirmed (owner accepts) -> completed (customer pays)
     *          -> visited (booking date has passed, escrow released)
     *
     * Rows that were settled under the old two-state scheme - `completed`
     * meant "paid AND the date has passed" - migrate to `visited`, while a
     * completed booking whose date is still ahead stays completed (paid,
     * shoot upcoming).
     */
    public function up(): void
    {
        $this->setBookingStatusEnum(['pending', 'confirmed', 'completed', 'cancelled', 'expired', 'visited']);

        DB::table('bookings')
            ->where('status', 'completed')
            ->where('booking_date', '<=', now())
            ->update(['status' => 'visited']);
    }

    public function down(): void
    {
        // Rows already flipped to visited would violate the narrower enum, so
        // migrate them back to the state they left (completed, paid).
        DB::table('bookings')->where('status', 'visited')->update(['status' => 'completed']);

        $this->setBookingStatusEnum(['pending', 'confirmed', 'completed', 'cancelled', 'expired']);
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
