<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->decimal('cost_products',    10, 2)->default(0)->after('total');
            $table->decimal('cost_services',    10, 2)->default(0)->after('cost_products');
            $table->decimal('cost_consumables', 10, 2)->default(0)->after('cost_services');
            $table->decimal('cost_disposal',    10, 2)->default(0)->after('cost_consumables');
            $table->decimal('margin_amount',    10, 2)->nullable()->after('cost_disposal');
            $table->decimal('margin_pct',       5,  2)->nullable()->after('margin_amount');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'cost_products', 'cost_services', 'cost_consumables',
                'cost_disposal', 'margin_amount', 'margin_pct',
            ]);
        });
    }
};
