<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tire_disposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->unsignedSmallInteger('qty_total');          // tires removed from vehicle
            $table->unsignedSmallInteger('qty_client_kept')->default(0); // client took their old tires
            $table->unsignedSmallInteger('qty_for_disposal');   // = qty_total - qty_client_kept

            // Cost reference (snapshot from Setting at time of creation, never hardcoded)
            $table->decimal('cost_per_unit', 10, 2)->default(0);

            $table->enum('status', ['pending', 'partial', 'collected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'status']);
            $table->index('work_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tire_disposals');
    }
};
