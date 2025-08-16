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
        // Remove cashier_session_id column from payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['cashier_session_id']);
            $table->dropColumn('cashier_session_id');
        });

        // Drop cashier_sessions table
        Schema::dropIfExists('cashier_sessions');

        // Drop jobs table (queue system)
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('failed_jobs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate cashier_sessions table
        Schema::create('cashier_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opened_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_amount', 10, 2)->default(0);
            $table->decimal('closing_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Recreate jobs table
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        // Recreate failed_jobs table
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // Add cashier_session_id back to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('cashier_session_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
