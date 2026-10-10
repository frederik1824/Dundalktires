<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_closes', function (Blueprint $table) {
            $table->foreignId('opened_by')->nullable()->after('branch_id')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at')->nullable()->after('opened_by');
            $table->decimal('opening_fund', 12, 2)->default(0)->after('opened_at');
        });
    }

    public function down(): void
    {
        Schema::table('cash_closes', function (Blueprint $table) {
            $table->dropForeign(['opened_by']);
            $table->dropColumn(['opened_by', 'opened_at', 'opening_fund']);
        });
    }
};
