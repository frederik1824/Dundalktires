<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Consumables actually used in a work order — internal cost, NOT billed to client
        // (sold products go to work_order_products; this is workshop consumption)
        Schema::create('work_order_consumables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');                          // snapshot at time of use
            $table->string('unit', 20)->default('unit');     // snapshot of product unit
            $table->decimal('qty_used',   8, 3);
            $table->decimal('unit_cost', 10, 2);             // cost_price snapshot
            $table->decimal('total_cost', 10, 2);            // qty_used × unit_cost
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('work_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_consumables');
    }
};
