<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            // Polymorphic: purchase, invoice, work_order, manual_adjustment
            $table->nullableMorphs('reference');

            $table->string('type'); // purchase | sale | adjustment | return | correction | transfer
            $table->integer('qty_before');
            $table->integer('qty_change');   // positive = in, negative = out
            $table->integer('qty_after');
            $table->decimal('unit_cost', 10, 2)->nullable(); // cost at the time of movement
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void { Schema::dropIfExists('product_movements'); }
};
