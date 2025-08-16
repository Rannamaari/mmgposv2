<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('phone')->nullable();
            $t->timestamps();
        });

        Schema::create('motorcycles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $t->string('plate_no')->index();
            $t->string('model')->nullable();
            $t->timestamps();
        });

        Schema::create('services', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->decimal('default_price', 10, 2)->default(0);
            $t->string('category')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('parts', function (Blueprint $t) {
            $t->id();
            $t->string('sku')->unique();
            $t->string('name');
            $t->decimal('price', 10, 2);
            $t->decimal('cost', 10, 2)->default(0);
            $t->integer('stock_qty')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('work_orders', function (Blueprint $t) {
            $t->id();
            $t->string('ticket_no')->unique();
            $t->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $t->foreignId('motorcycle_id')->constrained()->cascadeOnDelete();
            $t->string('status')->default('pending');
            $t->foreignId('assigned_mechanic_id')->nullable()->constrained('users');
            $t->text('notes')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
        });

        Schema::create('work_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $t->string('item_type');  // service|part
            $t->unsignedBigInteger('item_id');
            $t->string('name_snapshot');
            $t->integer('qty')->default(1);
            $t->decimal('unit_price', 10, 2)->default(0);
            $t->decimal('line_total', 10, 2)->default(0);
            $t->boolean('installed')->default(false);
            $t->foreignId('mechanic_id')->nullable()->constrained('users');
            $t->timestamps();
        });

        Schema::create('invoices', function (Blueprint $t) {
            $t->id();
            $t->string('number')->unique();
            $t->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $t->decimal('subtotal', 10, 2)->default(0);
            $t->decimal('discount', 10, 2)->default(0);
            $t->decimal('tax', 10, 2)->default(0);
            $t->decimal('total', 10, 2)->default(0);
            $t->string('status')->default('paid');
            $t->timestamps();
        });

        Schema::create('cashier_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('opened_by')->constrained('users');
            $t->decimal('opening_cash', 10, 2)->default(0);
            $t->timestamp('opened_at');
            $t->foreignId('closed_by')->nullable()->constrained('users');
            $t->decimal('counted_cash', 10, 2)->nullable();
            $t->decimal('expected_cash', 10, 2)->nullable();
            $t->decimal('variance', 10, 2)->nullable();
            $t->timestamp('closed_at')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_sessions');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('work_order_items');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('parts');
        Schema::dropIfExists('services');
        Schema::dropIfExists('motorcycles');
        Schema::dropIfExists('customers');
    }
};

