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
        Schema::table('property_accommodations', function (Blueprint $table) {
            $table->decimal('size', 8, 2)->nullable()->after('base_price')->comment('Size in square feet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_accommodations', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
};
