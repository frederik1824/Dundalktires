<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('number', 30)->unique();
            $table->enum('reason', ['return', 'error', 'discount', 'other']);
            $table->string('notes', 500)->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('refund_method', ['cash', 'card', 'zelle', 'cashapp', 'transfer', 'store_credit', 'other']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
