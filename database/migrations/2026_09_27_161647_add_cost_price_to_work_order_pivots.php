<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_order_services', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->default(0)->after('unit_price');
        });

        Schema::table('work_order_products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->default(0)->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('work_order_services', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
        Schema::table('work_order_products', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
    }
};
