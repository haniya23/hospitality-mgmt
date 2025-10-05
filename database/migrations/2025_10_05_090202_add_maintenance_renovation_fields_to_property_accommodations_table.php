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
            // Maintenance fields
            $table->enum('maintenance_status', ['none', 'scheduled', 'active', 'completed'])->default('none')->after('is_active');
            $table->date('maintenance_start_date')->nullable()->after('maintenance_status');
            $table->date('maintenance_end_date')->nullable()->after('maintenance_start_date');
            $table->text('maintenance_description')->nullable()->after('maintenance_end_date');
            $table->decimal('maintenance_cost', 10, 2)->nullable()->after('maintenance_description');
            
            // Renovation fields
            $table->enum('renovation_status', ['none', 'scheduled', 'active', 'completed'])->default('none')->after('maintenance_cost');
            $table->date('renovation_start_date')->nullable()->after('renovation_status');
            $table->date('renovation_end_date')->nullable()->after('renovation_start_date');
            $table->text('renovation_description')->nullable()->after('renovation_end_date');
            $table->decimal('renovation_cost', 10, 2)->nullable()->after('renovation_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_accommodations', function (Blueprint $table) {
            $table->dropColumn([
                'maintenance_status',
                'maintenance_start_date',
                'maintenance_end_date',
                'maintenance_description',
                'maintenance_cost',
                'renovation_status',
                'renovation_start_date',
                'renovation_end_date',
                'renovation_description',
                'renovation_cost'
            ]);
        });
    }
};
