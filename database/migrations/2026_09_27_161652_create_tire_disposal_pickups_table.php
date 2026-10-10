<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tire_disposal_pickups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // Optional link to expenses table so the cost appears in financial reports
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();

            $table->string('vendor')->nullable();             // hauler/recycler name
            $table->date('pickup_date');
            $table->unsignedSmallInteger('qty_collected');

            $table->decimal('cost_per_unit', 10, 2)->default(0); // paid per tire (0 if they pay you)
            $table->decimal('total_cost',    10, 2)->default(0); // qty_collected × cost_per_unit

            $table->enum('payment_method', ['cash', 'card', 'zelle', 'cashapp', 'transfer', 'check', 'other'])
                  ->default('cash');
            $table->string('evidence')->nullable();           // uploaded receipt file path
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'pickup_date']);
        });

        // Pivot: which tire_disposal batches were resolved by a pickup
        Schema::create('tire_disposal_pickup_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_id')->constrained('tire_disposal_pickups')->cascadeOnDelete();
            $table->foreignId('tire_disposal_id')->constrained('tire_disposals')->cascadeOnDelete();
            $table->unsignedSmallInteger('qty_collected');
            $table->timestamps();

            $table->unique(['pickup_id', 'tire_disposal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tire_disposal_pickup_items');
        Schema::dropIfExists('tire_disposal_pickups');
    }
};
