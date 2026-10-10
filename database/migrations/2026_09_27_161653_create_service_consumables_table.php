<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Default consumables suggested for each service (e.g. valve stems, balancing weights)
        Schema::create('service_consumables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // must be type='consumable'
            $table->decimal('qty_default', 8, 3)->default(1); // supports fractions (e.g. 0.5 L of lubricant)
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['service_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_consumables');
    }
};
