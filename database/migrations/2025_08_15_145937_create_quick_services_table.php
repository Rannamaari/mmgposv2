<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quick_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('default_price', 10, 2)->default(0);
            $table->string('category')->nullable(); // e.g., 'service', 'part', 'custom'
            $table->string('item_type')->nullable(); // 'service', 'part', or null for custom
            $table->unsignedBigInteger('item_id')->nullable(); // reference to service or part
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quick_services');
    }
};
