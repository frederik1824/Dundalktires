<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_closes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');

            // System totals (from payments, computed at close time)
            $table->decimal('system_total', 12, 2)->default(0);
            $table->json('system_amounts')->nullable(); // per-method: {cash:100, card:200, ...}

            // Physical amounts entered by the cashier
            $table->decimal('physical_total', 12, 2)->nullable();
            $table->json('physical_amounts')->nullable();

            // Difference = physical_total - system_total (can be negative)
            $table->decimal('difference', 12, 2)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_closes');
    }
};
