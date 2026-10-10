<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_price_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('vehicle_type'); // sedan, suv, pickup, van, truck, other
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['service_id', 'vehicle_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_price_rules');
    }
};
