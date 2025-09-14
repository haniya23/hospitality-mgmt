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
        Schema::table('pricing_rules', function (Blueprint $table) {
            $table->foreignId('accommodation_id')->nullable()->after('property_id')->constrained('property_accommodations')->cascadeOnDelete();
            $table->index(['property_id', 'accommodation_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_rules', function (Blueprint $table) {
            $table->dropForeign(['accommodation_id']);
            $table->dropIndex(['property_id', 'accommodation_id', 'is_active']);
            $table->dropColumn('accommodation_id');
        });
    }
};