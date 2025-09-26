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
        // Generate UUIDs for existing records (column already exists)
        $accommodations = \DB::table('property_accommodations')->whereNull('uuid')->get();
        foreach ($accommodations as $accommodation) {
            \DB::table('property_accommodations')
                ->where('id', $accommodation->id)
                ->update(['uuid' => \Illuminate\Support\Str::uuid()]);
        }

        // Add unique constraint
        Schema::table('property_accommodations', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_accommodations', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};