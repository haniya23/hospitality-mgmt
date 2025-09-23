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
        Schema::table('subscription_requests', function (Blueprint $table) {
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly')->after('requested_plan');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_requests', function (Blueprint $table) {
            $table->dropColumn('billing_cycle');
        });
    }
};
