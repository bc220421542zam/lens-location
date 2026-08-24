<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // No after() clauses: this database's users table has drifted from
            // its own migration (no google_id / business_name), so positional
            // anchors are unreliable here.
            $table->string('stripe_account_id')->nullable();

            // Accounts v2 exposes readiness as a capability status string
            // ('active', 'pending', 'restricted', 'unsupported'), not the
            // deprecated charges_enabled/payouts_enabled booleans.
            $table->string('stripe_transfers_status')->nullable();
            $table->timestamp('stripe_onboarded_at')->nullable();

            $table->index('stripe_account_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['stripe_account_id']);
            $table->dropColumn([
                'stripe_account_id',
                'stripe_transfers_status',
                'stripe_onboarded_at',
            ]);
        });
    }
};
