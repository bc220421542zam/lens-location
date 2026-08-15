<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Covering indexes for the columns the dashboards and list screens actually
 * filter and sort on. Foreign keys already carry single-column indexes, so
 * everything here is either a composite that matches a real query shape or a
 * plain column that had no index at all.
 *
 * Both directions are guarded with hasIndex(), so a run that fails part-way
 * through can simply be re-run instead of leaving the schema stranded.
 */
return new class extends Migration
{
    /**
     * Indexes this migration adds: index name => [table, columns].
     *
     * @var array<string, array{0: string, 1: array<int, string>}>
     */
    private const ADDED = [
        // Browse: where status = approved ... latest()
        'locations_status_created_at_index'       => ['locations', ['status', 'created_at']],
        // Owner dashboard/listing counts: where user_id = ? and status = ?
        'locations_user_id_status_index'          => ['locations', ['user_id', 'status']],
        // Category filter and the distinct-category dropdown
        'locations_category_index'                => ['locations', ['category']],

        'bookings_customer_id_status_index'       => ['bookings', ['customer_id', 'status']],
        'bookings_location_id_status_index'       => ['bookings', ['location_id', 'status']],
        // "upcoming" filters and orders on booking_date
        'bookings_booking_date_index'             => ['bookings', ['booking_date']],

        // Owner earnings stats: owner_id + status + payout_status
        'transactions_owner_status_payout_index'  => ['transactions', ['owner_id', 'status', 'payout_status']],
        'transactions_customer_id_status_index'   => ['transactions', ['customer_id', 'status']],
        // Admin revenue stats scan by status alone
        'transactions_status_index'               => ['transactions', ['status']],
        // The payment gateway callback looks the row up by this reference
        'transactions_jazzcash_txn_ref_index'     => ['transactions', ['jazzcash_txn_ref']],

        'reviews_location_id_is_visible_index'    => ['reviews', ['location_id', 'is_visible']],

        // Marking a thread read: conversation_id + read_at IS NULL
        'messages_conversation_id_read_at_index'  => ['messages', ['conversation_id', 'read_at']],

        // The bell dropdown polls notifiable + latest() every 30s
        'notifications_notifiable_created_at_index' => ['notifications', ['notifiable_type', 'notifiable_id', 'created_at']],
    ];

    /**
     * Several composites above start with a foreign key column. MySQL treats
     * such a composite as satisfying the constraint and drops the single-column
     * index the foreign key created, then refuses to drop the composite because
     * that would leave the constraint unindexed. down() therefore restores each
     * of these under its original name before removing the composites.
     *
     * @var array<string, array{0: string, 1: string}>  index name => [table, column]
     */
    private const FOREIGN_KEY_INDEXES = [
        'locations_user_id_foreign'         => ['locations', 'user_id'],
        'bookings_customer_id_foreign'      => ['bookings', 'customer_id'],
        'bookings_location_id_foreign'      => ['bookings', 'location_id'],
        'transactions_owner_id_foreign'     => ['transactions', 'owner_id'],
        'transactions_customer_id_foreign'  => ['transactions', 'customer_id'],
        'reviews_location_id_foreign'       => ['reviews', 'location_id'],
        'messages_conversation_id_foreign'  => ['messages', 'conversation_id'],
    ];

    public function up(): void
    {
        foreach (self::ADDED as $name => [$table, $columns]) {
            if (Schema::hasIndex($table, $name)) {
                continue;
            }

            Schema::table($table, fn (Blueprint $t) => $t->index($columns, $name));
        }
    }

    public function down(): void
    {
        foreach (self::FOREIGN_KEY_INDEXES as $name => [$table, $column]) {
            if (Schema::hasIndex($table, $name)) {
                continue;
            }

            Schema::table($table, fn (Blueprint $t) => $t->index($column, $name));
        }

        foreach (self::ADDED as $name => [$table, $columns]) {
            if (! Schema::hasIndex($table, $name)) {
                continue;
            }

            Schema::table($table, fn (Blueprint $t) => $t->dropIndex($name));
        }
    }
};
