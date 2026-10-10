<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'zelle', 'cashapp', 'transfer', 'check', 'other'])->default('cash');
            $table->string('vendor')->nullable();
            $table->string('reference')->nullable();
            $table->string('evidence')->nullable(); // path to uploaded file
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence', ['weekly', 'biweekly', 'monthly', 'yearly'])->nullable();
            $table->date('paid_at');
            $table->date('next_due_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
