<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Customer indexes
        if (!Schema::hasIndex('customers', 'customers_name_index')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->index(['name']);
            });
        }
        if (!Schema::hasIndex('customers', 'customers_phone_index')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->index(['phone']);
            });
        }
        if (!Schema::hasIndex('customers', 'customers_created_at_index')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->index(['created_at']);
            });
        }

        // Motorcycle indexes
        if (!Schema::hasIndex('motorcycles', 'motorcycles_customer_id_plate_no_index')) {
            Schema::table('motorcycles', function (Blueprint $table) {
                $table->index(['customer_id', 'plate_no']);
            });
        }
        if (!Schema::hasIndex('motorcycles', 'motorcycles_plate_no_index')) {
            Schema::table('motorcycles', function (Blueprint $table) {
                $table->index(['plate_no']);
            });
        }
        if (!Schema::hasIndex('motorcycles', 'motorcycles_model_index')) {
            Schema::table('motorcycles', function (Blueprint $table) {
                $table->index(['model']);
            });
        }

        // Service indexes
        if (!Schema::hasIndex('services', 'services_is_active_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->index(['is_active']);
            });
        }
        if (!Schema::hasIndex('services', 'services_is_quick_service_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->index(['is_quick_service']);
            });
        }
        if (!Schema::hasIndex('services', 'services_category_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->index(['category']);
            });
        }
        if (!Schema::hasIndex('services', 'services_name_index')) {
            Schema::table('services', function (Blueprint $table) {
                $table->index(['name']);
            });
        }

        // Parts indexes
        if (!Schema::hasIndex('parts', 'parts_is_active_index')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->index(['is_active']);
            });
        }
        if (!Schema::hasIndex('parts', 'parts_is_quick_service_index')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->index(['is_quick_service']);
            });
        }
        if (!Schema::hasIndex('parts', 'parts_sku_index')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->index(['sku']);
            });
        }
        if (!Schema::hasIndex('parts', 'parts_name_index')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->index(['name']);
            });
        }
        if (!Schema::hasIndex('parts', 'parts_stock_qty_index')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->index(['stock_qty']);
            });
        }

        // Work Orders indexes
        if (!Schema::hasIndex('work_orders', 'work_orders_status_index')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->index(['status']);
            });
        }
        if (!Schema::hasIndex('work_orders', 'work_orders_customer_id_status_index')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->index(['customer_id', 'status']);
            });
        }
        if (!Schema::hasIndex('work_orders', 'work_orders_motorcycle_id_index')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->index(['motorcycle_id']);
            });
        }
        if (!Schema::hasIndex('work_orders', 'work_orders_created_at_index')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->index(['created_at']);
            });
        }
        if (!Schema::hasIndex('work_orders', 'work_orders_ticket_no_index')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->index(['ticket_no']);
            });
        }

        // Invoices indexes
        if (!Schema::hasIndex('invoices', 'invoices_status_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['status']);
            });
        }
        if (!Schema::hasIndex('invoices', 'invoices_customer_id_status_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['customer_id', 'status']);
            });
        }
        if (!Schema::hasIndex('invoices', 'invoices_motorcycle_id_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['motorcycle_id']);
            });
        }
        if (!Schema::hasIndex('invoices', 'invoices_created_at_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['created_at']);
            });
        }
        if (!Schema::hasIndex('invoices', 'invoices_number_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['number']);
            });
        }

        // Payments indexes
        if (!Schema::hasIndex('payments', 'payments_invoice_id_index')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['invoice_id']);
            });
        }
        if (!Schema::hasIndex('payments', 'payments_method_index')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['method']);
            });
        }
        if (!Schema::hasIndex('payments', 'payments_received_at_index')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['received_at']);
            });
        }
        if (!Schema::hasIndex('payments', 'payments_received_by_index')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['received_by']);
            });
        }

        // Work Order Items indexes
        if (!Schema::hasIndex('work_order_items', 'work_order_items_work_order_id_index')) {
            Schema::table('work_order_items', function (Blueprint $table) {
                $table->index(['work_order_id']);
            });
        }
        if (!Schema::hasIndex('work_order_items', 'work_order_items_item_type_item_id_index')) {
            Schema::table('work_order_items', function (Blueprint $table) {
                $table->index(['item_type', 'item_id']);
            });
        }
        if (!Schema::hasIndex('work_order_items', 'work_order_items_installed_index')) {
            Schema::table('work_order_items', function (Blueprint $table) {
                $table->index(['installed']);
            });
        }

        // Inventory Movements indexes
        if (!Schema::hasIndex('inventory_movements', 'inventory_movements_part_id_index')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                $table->index(['part_id']);
            });
        }
        if (!Schema::hasIndex('inventory_movements', 'inventory_movements_reason_index')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                $table->index(['reason']);
            });
        }
        if (!Schema::hasIndex('inventory_movements', 'inventory_movements_ref_type_ref_id_index')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                $table->index(['ref_type', 'ref_id']);
            });
        }
        if (!Schema::hasIndex('inventory_movements', 'inventory_movements_created_at_index')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                $table->index(['created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'plate_no']);
            $table->dropIndex(['plate_no']);
            $table->dropIndex(['model']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_quick_service']);
            $table->dropIndex(['category']);
            $table->dropIndex(['name']);
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_quick_service']);
            $table->dropIndex(['sku']);
            $table->dropIndex(['name']);
            $table->dropIndex(['stock_qty']);
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['customer_id', 'status']);
            $table->dropIndex(['motorcycle_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['ticket_no']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['customer_id', 'status']);
            $table->dropIndex(['motorcycle_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['number']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['invoice_id']);
            $table->dropIndex(['method']);
            $table->dropIndex(['received_at']);
            $table->dropIndex(['received_by']);
        });

        Schema::table('work_order_items', function (Blueprint $table) {
            $table->dropIndex(['work_order_id']);
            $table->dropIndex(['item_type', 'item_id']);
            $table->dropIndex(['installed']);
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropIndex(['part_id']);
            $table->dropIndex(['reason']);
            $table->dropIndex(['ref_type', 'ref_id']);
            $table->dropIndex(['created_at']);
        });
    }
};