<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The 2026_08_15 performance-indexes migration indexed the old column
        // name. Drop it before renaming, then re-add under the new name.
        if (Schema::hasIndex('transactions', 'transactions_jazzcash_txn_ref_index')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropIndex('transactions_jazzcash_txn_ref_index');
            });
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('jazzcash_txn_ref', 'gateway_ref');
        });

        // No after() clauses: this database has drifted from its migrations, so
        // positional anchors aren't reliable here.
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('gateway_ref', 'transactions_gateway_ref_index');

            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_checkout_session_id')->nullable();
            $table->string('stripe_transfer_id')->nullable();

            // Stored rather than derived, so amount = platform_fee + owner_earning
            // always holds even if the commission rate changes later.
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->char('currency', 3)->default('PKR');
            $table->timestamp('paid_at')->nullable();

            $table->unique('stripe_payment_intent_id', 'transactions_stripe_pi_unique');
            $table->index('stripe_checkout_session_id', 'transactions_stripe_cs_index');
        });

        $this->setStatusEnum(['pending', 'paid', 'failed', 'refunded']);
    }

    public function down(): void
    {
        DB::table('transactions')->where('status', 'refunded')->update(['status' => 'failed']);
        $this->setStatusEnum(['pending', 'paid', 'failed']);

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('transactions_stripe_pi_unique');
            $table->dropIndex('transactions_stripe_cs_index');
            $table->dropIndex('transactions_gateway_ref_index');
            $table->dropColumn([
                'stripe_payment_intent_id',
                'stripe_checkout_session_id',
                'stripe_transfer_id',
                'platform_fee',
                'currency',
                'paid_at',
            ]);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('gateway_ref', 'jazzcash_txn_ref');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('jazzcash_txn_ref', 'transactions_jazzcash_txn_ref_index');
        });
    }

    /**
     * `status` is a constrained column on both drivers, so admitting 'refunded'
     * needs a schema change on each - MySQL stores a native ENUM, and Laravel
     * implements enum() on SQLite as a CHECK constraint.
     *
     * On SQLite the column becomes a plain string: the PHP PaymentStatus enum
     * is the real source of truth, and this keeps the test database from
     * needing a DDL edit every time a status is added.
     */
    private function setStatusEnum(array $values): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $list = implode(',', array_map(fn ($v) => "'".$v."'", $values));

            DB::statement("ALTER TABLE transactions MODIFY status ENUM({$list}) NOT NULL DEFAULT 'pending'");

            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
