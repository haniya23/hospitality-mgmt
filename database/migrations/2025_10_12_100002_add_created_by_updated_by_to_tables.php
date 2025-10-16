<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add created_by and updated_by to all important tables
        $tables = [
            'reservations',
            'guests',
            'properties',
            'property_accommodations',
            'payments',
            'invoices',
            'tasks',
            'task_logs',
            'check_ins',
            'check_outs',
            'staff_members',
            'staff_attendance',
            'staff_leave_requests',
            'staff_performance_reviews',
            'maintenance_tickets',
            'property_photos',
            'pricing_rules',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
                    }
                    if (!Schema::hasColumn($table->getTable(), 'updated_by')) {
                        $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'reservations',
            'guests',
            'properties',
            'property_accommodations',
            'payments',
            'invoices',
            'tasks',
            'task_logs',
            'check_ins',
            'check_outs',
            'staff_members',
            'staff_attendance',
            'staff_leave_requests',
            'staff_performance_reviews',
            'maintenance_tickets',
            'property_photos',
            'pricing_rules',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->dropForeign(['created_by']);
                        $table->dropColumn('created_by');
                    }
                    if (Schema::hasColumn($table->getTable(), 'updated_by')) {
                        $table->dropForeign(['updated_by']);
                        $table->dropColumn('updated_by');
                    }
                });
            }
        }
    }
};


