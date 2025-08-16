<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean up all invoice-related data in the correct order
        // (respecting foreign key constraints)

        // 1. Delete inventory movements (related to parts sales)
        DB::table('inventory_movements')->truncate();

        // 2. Delete work order items (linked to invoices)
        DB::table('work_order_items')->truncate();

        // 3. Delete payments (linked to invoices)
        DB::table('payments')->truncate();

        // 4. Delete invoices
        DB::table('invoices')->truncate();

        // Reset auto-increment counters (SQLite compatible)
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('DELETE FROM sqlite_sequence WHERE name IN ("invoices", "payments", "work_order_items", "inventory_movements")');
        } else {
            DB::statement('ALTER SEQUENCE invoices_id_seq RESTART WITH 1');
            DB::statement('ALTER SEQUENCE payments_id_seq RESTART WITH 1');
            DB::statement('ALTER SEQUENCE work_order_items_id_seq RESTART WITH 1');
            DB::statement('ALTER SEQUENCE inventory_movements_id_seq RESTART WITH 1');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is destructive, so we can't reverse it
        // Data will need to be recreated manually
    }
};
