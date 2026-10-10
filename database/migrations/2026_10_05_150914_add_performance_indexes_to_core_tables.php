<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['branch_id', 'status', 'paid_at'], 'idx_invoices_branch_status_paid');
            $table->index('created_at', 'idx_invoices_created_at');
        });

        // 2. Invoice Items
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->index(['type', 'reference_id'], 'idx_invoice_items_type_ref');
        });

        // 3. Work Orders
        Schema::table('work_orders', function (Blueprint $table) {
            $table->index(['branch_id', 'status', 'created_at'], 'idx_wo_branch_status_created');
        });

        // 4. Products
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'type'], 'idx_products_active_type');
            $table->index('is_featured', 'idx_products_is_featured');
            $table->index('sku', 'idx_products_sku');
            $table->index('name', 'idx_products_name');
        });

        // 5. Web Leads
        Schema::table('web_leads', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_web_leads_status_created');
        });

        // 6. Appointments
        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['branch_id', 'scheduled_at', 'status'], 'idx_appointments_branch_sched_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_branch_status_paid');
            $table->dropIndex('idx_invoices_created_at');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropIndex('idx_invoice_items_type_ref');
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropIndex('idx_wo_branch_status_created');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active_type');
            $table->dropIndex('idx_products_is_featured');
            $table->dropIndex('idx_products_sku');
            $table->dropIndex('idx_products_name');
        });

        Schema::table('web_leads', function (Blueprint $table) {
            $table->dropIndex('idx_web_leads_status_created');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_appointments_branch_sched_status');
        });
    }
};
