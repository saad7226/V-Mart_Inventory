<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: We intentionally use NO foreign key constraints anywhere in this
     * migration. InfinityFree's shared MySQL environment rejects circular and
     * cross-table FK chains (errno 150). Referential integrity is enforced at
     * the application layer (DB transactions in AuthController).
     */
    public function up(): void
    {
        $tables = [
            'users',
            'products',
            'categories',
            'brands',
            'units',
            'customers',
            'orders',
            'order_products',
            'order_transactions',
            'suppliers',
            'purchases',
            'purchase_items',
        ];

        foreach ($tables as $tableName) {
            if (
                Schema::hasTable($tableName) &&
                !Schema::hasColumn($tableName, 'store_id')
            ) {
                Schema::table($tableName, function (Blueprint $table) {
                    // Plain nullable integer — no FK constraint.
                    // Application code guarantees store_id is always valid.
                    $table->unsignedBigInteger('store_id')->nullable()->index();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'products',
            'categories',
            'brands',
            'units',
            'customers',
            'orders',
            'order_products',
            'order_transactions',
            'suppliers',
            'purchases',
            'purchase_items',
        ];

        foreach ($tables as $tableName) {
            if (
                Schema::hasTable($tableName) &&
                Schema::hasColumn($tableName, 'store_id')
            ) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('store_id');
                });
            }
        }
    }
};
