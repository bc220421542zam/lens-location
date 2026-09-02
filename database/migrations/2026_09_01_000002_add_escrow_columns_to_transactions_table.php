<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('platform_commission', 10, 2)->default(0);
            $table->decimal('owner_payout_amount', 10, 2)->default(0);
            $table->timestamp('held_since')->nullable();
        });

        // Widen to a superset FIRST so existing 'unpaid'/'paid' rows survive
        // the ALTER (MySQL strict mode rejects values outside the new ENUM).
        $this->setPayoutStatusEnum(['unpaid', 'paid', 'held', 'eligible', 'paid_out']);

        DB::table('transactions')->where('payout_status', 'unpaid')->update(['payout_status' => 'held']);
        DB::table('transactions')->where('payout_status', 'paid')->update(['payout_status' => 'paid_out']);

        $this->setPayoutStatusEnum(['held', 'eligible', 'paid_out']);

        // Backfill the escrow split from the legacy destination-charge columns
        // and stamp held_since from the payment timestamp.
        DB::table('transactions')->where('platform_commission', 0)->update([
            'platform_commission' => DB::raw('platform_fee'),
        ]);
        DB::table('transactions')->where('owner_payout_amount', 0)->update([
            'owner_payout_amount' => DB::raw('owner_earning'),
        ]);
        DB::table('transactions')->whereNull('held_since')->whereNotNull('paid_at')->update([
            'held_since' => DB::raw('paid_at'),
        ]);
    }

    public function down(): void
    {
        $this->setPayoutStatusEnum(['unpaid', 'paid', 'held', 'eligible', 'paid_out']);
        DB::table('transactions')->where('payout_status', 'held')->update(['payout_status' => 'unpaid']);
        DB::table('transactions')->whereIn('payout_status', ['eligible', 'paid_out'])->update(['payout_status' => 'paid']);
        $this->setPayoutStatusEnum(['unpaid', 'paid']);

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['platform_commission', 'owner_payout_amount', 'held_since']);
        });
    }

    /**
     * Driver-branch enum change copied from the 2026_08_24_100100 migration:
     * MySQL keeps a native ENUM, SQLite converts to a plain string column.
     */
    private function setPayoutStatusEnum(array $values): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $quoted = collect($values)->map(fn ($v) => "'{$v}'")->join(', ');
            DB::statement("ALTER TABLE `transactions` MODIFY `payout_status` ENUM({$quoted}) NOT NULL DEFAULT 'held'");

            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payout_status')->default('held')->change();
        });
    }
};
