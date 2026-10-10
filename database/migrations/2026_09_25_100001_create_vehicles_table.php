<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('make');        // Toyota
            $table->string('model');       // Camry
            $table->year('year');
            $table->string('color')->nullable();
            $table->string('plate')->nullable();
            $table->string('vin', 17)->nullable();
            $table->string('type')->default('sedan'); // sedan, suv, pickup, van, truck, other
            $table->string('tire_size')->nullable();   // 215/55R17
            $table->unsignedInteger('mileage')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('plate');
            $table->index(['make', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
