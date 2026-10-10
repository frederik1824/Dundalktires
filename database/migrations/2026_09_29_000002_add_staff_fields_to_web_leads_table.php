<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_leads', function (Blueprint $table) {
            $table->timestamp('contacted_at')->nullable()->after('status');
            $table->text('staff_notes')->nullable()->after('contacted_at');
        });
    }

    public function down(): void
    {
        Schema::table('web_leads', function (Blueprint $table) {
            $table->dropColumn(['contacted_at', 'staff_notes']);
        });
    }
};
