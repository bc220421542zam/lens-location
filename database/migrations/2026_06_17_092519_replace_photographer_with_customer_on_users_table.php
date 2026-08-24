<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL stores `role` as a native ENUM, so widening it to admit
        // 'customer' and then narrowing it again needs DDL. SQLite (used by the
        // test suite) has no ENUM type - the column is already a plain string,
        // so only the data migration applies there.
        $this->setRoleEnum(['admin', 'owner', 'photographer', 'customer']);

        DB::table('users')->where('role', 'photographer')->update(['role' => 'customer']);

        $this->setRoleEnum(['admin', 'owner', 'customer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->setRoleEnum(['admin', 'owner', 'photographer', 'customer']);

        DB::table('users')->where('role', 'customer')->update(['role' => 'photographer']);

        $this->setRoleEnum(['admin', 'owner', 'photographer']);
    }

    private function setRoleEnum(array $values): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $list = implode(', ', array_map(fn ($v) => "'".$v."'", $values));

        DB::statement("ALTER TABLE users MODIFY role ENUM({$list}) NOT NULL DEFAULT 'admin'");
    }
};
