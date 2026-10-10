<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();

            // Product type
            $table->string('type')->default('tire'); // tire | material | consumable | part

            // General
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit', 20)->default('unit'); // unit, pair, set, liter, kg, box...

            // Tire-specific fields
            $table->string('tire_brand')->nullable();
            $table->string('tire_line')->nullable();    // model line (N-Priz AH8)
            $table->string('tire_width')->nullable();   // 215
            $table->string('tire_ratio')->nullable();   // 55
            $table->string('tire_diameter')->nullable(); // 17
            $table->string('tire_type')->nullable();    // All-Season, Summer, Winter, All-Terrain
            $table->string('tire_size_normalized')->nullable()->index(); // "21555R17" for fast search

            // Pricing
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);

            // Stock
            $table->integer('stock_qty')->default(0);
            $table->integer('stock_min')->default(0);   // alert threshold
            $table->string('location')->nullable();     // warehouse location (A1, B3...)

            // Meta
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('tire_brand');
            // composite tire index removed: 3 varchar cols exceed MariaDB 1000-byte limit
        });
    }

    public function down(): void { Schema::dropIfExists('products'); }
};
