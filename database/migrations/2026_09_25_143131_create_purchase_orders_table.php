<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('number')->unique();             // PO-001, PO-002…
            // draft → sent → partial → received | cancelled
            $table->string('status')->default('draft');
            $table->string('type')->default('regular');    // regular | suggested

            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();       // stored path to scanned doc

            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('supplier_id');
        });
    }

    public function down(): void { Schema::dropIfExists('purchase_orders'); }
};
