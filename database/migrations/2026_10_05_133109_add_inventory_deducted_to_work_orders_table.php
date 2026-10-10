<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->boolean('inventory_deducted')->default(false)->after('invoiced');
        });

        // Backfill existing completed or invoiced work orders
        DB::table('work_orders')
            ->where('status', 'completed')
            ->orWhere('invoiced', true)
            ->update(['inventory_deducted' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('inventory_deducted');
        });
    }
};
